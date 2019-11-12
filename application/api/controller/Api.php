<?php


namespace app\api\controller;


use app\api\common\AsyncCommand;
use app\api\common\Sms;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\Config;
use think\Controller;
use think\exception\ErrorException;

class Api extends Controller
{
    /**
     * Notes:上传公共接口
     * 在config.php文件中配置use_qiniu选项调节是否开启七牛云上传功能
     * @param type 上传类型  可选（video，img），可通过api/config.php文件进行配置上传类型以及其后缀
     * User: BigNiu
     * Date: 2019/10/8
     * Time: 16:01
     * @return \think\response\Json
     * @throws \Exception
     */
    public function upload()
    {
        //dump(Config::has('use_qiniu'));exit;
        //如果开启使用七牛云上传
        if (Config::get("use_qiniu")) {
            return $this->upload_qiniu();
        }
        $type = input("type");
        $config = Config::get($type);
        if (!$config) {
            return error("上传类型错误");
        }
        // 获取表单上传视频 例如上传了001.mp4
        $file = request()->file('file');
        if (!$file) {
            return error("请选择上传文件");
        }
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['ext' => $config['ext']])->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $type);

        if ($info) {
            $url = 'uploads/' . $type . "/" . str_replace(DS, "/", $info->getSaveName());
            if ($type == 'video') {
                $data = [
                    'url' => $url,
                    'img' => getImg($url)
                ];
                return success("上传成功", $data);
            } else {
                $data = [
                    'url' => $url
                ];
                return success("上传成功", $data);
            }
            //上传成功返回路径

        } else {
            // 上传失败获取错误信息
            return error($file->getError());
        }
    }

    /**
     * Notes:七牛云上传
     * User: BigNiu
     * Date: 2019/10/8
     * Time: 16:00
     * @return \think\response\Json 图片完整URL
     * @throws \Exception
     */
    public function upload_qiniu()
    {
        if (request()->isPost()) {
            $type = input("type");
            $config = Config::get($type);
            if (!$config) {
                return error("上传类型错误");
            }
            $file = request()->file('file');
            // 要上传图片的本地路径
            $filePath = $file->getRealPath();
            $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀
            //获取当前控制器名称
            // 上传到七牛后保存的文件名
            $key = substr(md5($file->getRealPath()), 0, 5) . date('YmdHis') . rand(0, 9999) . '.' . $ext;
            require_once APP_PATH . '/../vendor/qiniu/autoload.php';
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey = Config::get('ACCESSKEY');
            $secretKey = Config::get('SECRETKEY');
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 要上传的空间
            $bucket = Config::get('BUCKET');
            $domain = Config::get('DOMAIN');
            $token = $auth->uploadToken($bucket);
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                return error($err);
            } else {
                $url = "http://" . $domain . "/" . $ret['key'];
                if ($type == 'video') {
                    $data = [
                        'url' => $url,
                        'img' => getImg($url)
                    ];
                    return success("上传成功", $data);
                } else {
                    $data = [
                        'url' => $url
                    ];
                    return success("上传成功", $data);
                }
                //返回图片的完整URL
                return success("上传成功",$data );
            }
        }
    }

    /**
     * Notes:检测更新
     * User: BigNiu
     * Date: 2019/10/30
     * Time: 10:48
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function update()
    {
        $appid = input('appid');
        $version = input('version');
        $os = input("os");
        $update = Db("update")->where(['appid' => $appid])->order('version desc')->find();
        $pattern = '/^\d+\.\d+.\d+$/';//需要转义/
        preg_match($pattern, $version, $match);
        if (!$match) {
            return error("您的版本号不符合规范，格式为: 1.1.1");
        }
        //未找到新版本信息，直接返回最新版
        if (!$update) {
            return error("暂无更新");
        }
        $newVersion = $update['version'];
        $newVersion = explode(".", $newVersion);

        $newVersion1 = $newVersion[0];
        $newVersion2 = $newVersion[1];
        $newVersion3 = $newVersion[2];

        $version = explode('.', $version);
        $version1 = $version[0];
        $version2 = $version[1];
        $version3 = $version[2];
        //主版本号大于当前版本
        if ($newVersion1 > $version1) {
            $data = [
                "status" => 1,//升级标志，1：需要升级；0：无需升级
                "note" => $update['content'],//release notes
                "url" => $os == 'Android' ? $update['android_download'] : $update['ios_download'], //更新包下载地址
                "open_type" => $os == 'Android' ? $update['open_type'] : 2//打开方式，安卓独有。IOS默认外部打开
            ];
            return success("成功", $data);
        }
        if ($newVersion1 == $version1 && $newVersion2 > $version2) {
            $data = [
                "status" => 1,//升级标志，1：需要升级；0：无需升级
                "note" => $update['content'],//release notes
                "url" => $os == 'Android' ? $update['android_download'] : $update['ios_download'], //更新包下载地址
                "open_type" => $os == 'Android' ? $update['open_type'] : 2//打开方式，安卓独有。IOS默认外部打开
            ];
            return success("成功", $data);
        }
        if ($newVersion1 == $version1 && $newVersion2 == $version2 && $newVersion3 > $version3) {
            $data = [
                "status" => 1,//升级标志，1：需要升级；0：无需升级
                "note" => $update['content'],//release notes
                "url" => $os == 'Android' ? $update['android_download'] : $update['ios_download'], //更新包下载地址
                "open_type" => $os == 'Android' ? $update['open_type'] : 2//打开方式，安卓独有。IOS默认外部打开
            ];
            return success("成功", $data);
        }
        if ($newVersion1 == $version1 && $newVersion2 == $version2 && $newVersion3 == $version3) {
            return error("暂无更新");
        }
        //都小于当前版本
        return error("暂无更新");
    }

    /**
     * Notes:测试ffmpeg截图
     * User: BigNiu
     * Date: 2019/10/8
     * Time: 16:03
     */
    public function test()
    {
        /* $url = "uploads/video/5d9195f335cd7.mp4";
         $cmd = "ffmpeg -i ".str_replace("&","",$url)." -ss 00:00:00 -t 1 uploads/img/".md5($url).".png";
         $res = shell_exec($cmd);
         var_dump($res);*/
        $url = "D:/1.mp4";
        $start = time();
        $cmd = "attrib +R {$url}&&ffmpeg -i {$url} -b 600k D:/111.mp4&&attrib -R {$url}";
        $res = AsyncCommand::run($cmd);
//        shell_exec($cmd);
        $end = time();
        echo "执行时间:" . ($end - $start);
        echo "\n" . $res;
    }

    public function caiji()
    {
        set_time_limit(0);
        $id = 138400;

        for ($i = 0; $i < 100; $i++) {
            $id += 10;
            $insertData = [];
            //echo "=================https://api.apiopen.top/videoRecommend?id={$id}=============<br/>";
            $data = json_decode(file_get_contents("https://api.apiopen.top/videoRecommend?id={$id}"));
            if ($data->code == 400) {
                continue;
            }
            $result = $data->result;
            foreach ($result as $item) {
                $item_data = $item->data;
                if ($item->type == 'videoSmallCard') {
                    $url = $item_data->playUrl;
                    $title = $item_data->title;
                    // echo $title."<br/>";
                    $img = $item_data->cover->detail;
                    $insert = [
                        'url' => $url,
                        'img' => $img,
                        'title' => $title,
                        'type' => rand(127, 141),
                        'uid' => rand(10, 26),
                        'create_time' => TIME
                    ];
                    array_push($insertData, $insert);

                }
            }
            Db("video")->insertAll($insertData);
        }
    }

    public function sms()
    {
        $phone = input('phone/i');
        if (Sms::sendSms($phone, rand(100000, 999999))) {
            return success("发送成功");
        }
        return error("发送失败");
    }

    public function updateUserAvater()
    {
        $userList = Db("user")->select();
        $dir = scandir("static\avatar");
        foreach ($userList as $key => $value) {
            do {
                $name = $dir[intval(rand(0, sizeof($dir)))];
            } while ($name == '.' || $name == "..");
            Db("user")->where(['id' => $value['id']])->update(['head_img' => "static/avatar/" . $name]);
            echo "更新用户" . $value['phone'] . "头像为" . "static/avatar/" . $name . "成功<br/>";
        }
    }

}
<?php
require_once "../vendor/autoload.php";
require "../lib/AliOss.php";

use Medoo\Medoo;

ini_set("display_errors", 1);
error_reporting(E_ALL);

class Conversion
{
    protected $db;
    public function __construct()
    {
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'gaoxiao',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => 'root',
        ]);
    }

    /**
     * 处理所有的url
     */
    public function dealWithAll()
    {
        $page = 1;
        $size = 15;

        while (true) {
            $start = ($page - 1) * $size;
            $content = $this->db->query(
                "SELECT * FROM gx_content where  is_deal = 0  limit " . $start . "," . $size
            )->fetchAll();

            $aliOss = new AliOss();
            foreach ($content as $key => $val) {
                $this->db->action(function ($database) use ($aliOss, $val) {
                    try {

                        echo "处理第" . $val["id"] . "个---标题为:" . $val["title"] . "\r\n";

                        $author = $this->db->rand("gx_user", "id");
                        $authorId = array_rand($author, 1); //生成的作者的id

                        //查询出来当前的数据关联的media的类型
                        $sign = $val['sign'];
                        $mediaData = $database->select("gx_media", "*", ["content_sign" => $sign]);
                        if ($mediaData) {
                            $type = 0;
                            foreach ($mediaData as $meida) {
                                //var_dump($meida);
                                if (isset($meida['cover_url']) && $meida['cover_url'] != "") {
                                    try {
                                        $coverSaveFile = $aliOss->uploadFile($meida['cover_url']);
                                    } catch (Exception $e) {
                                        return false;
                                    }
                                } else {
                                    $coverSaveFile = "";
                                }

                                if (isset($meida['original_url']) && $meida['original_url'] != "") {
                                    try {
                                        $originalSaveFile = $aliOss->uploadFile($meida['original_url']);
                                    } catch (Exception $e) {
                                        return false;
                                    }
                                } else {
                                    $originalSaveFile = "";
                                }
                                $type = $meida['type'];

                                $database->update("gx_media", [
                                    "o_cover_url" => $coverSaveFile,
                                    "o_original_url" => $originalSaveFile,
                                ], [
                                    "id" => $meida["id"],
                                ]);
                            }
                            $database->update("gx_content", [
                                "update_type" => $type,
                                "is_deal" => 1,
                                "author_id" => $authorId,
                            ], ["id" => $val['id']]);
                        } else {
                            $database->update("gx_content", [
                                "update_type" => 4,
                                "is_deal" => 1,
                                "author_id" => $authorId,
                            ], ["id" => $val['id']]);
                        }
                    } catch (Exection $e) {
                        return false;
                    }
                });
            }

            sleep(1);
        }
    }

    public function dealWithUser()
    {
        $page = 2;
        $size = 15;

        while (true) {
            $start = ($page - 1) * $size;
            $data = $this->db->query(
                "SELECT * FROM tbk_tmp_user limit " . $start . "," . $size
            )->fetchAll();
            foreach ($data as $key => $val) {
                $this->db->insert(
                    "gx_user",
                    [
                        "nickname" => $val["nickname"],
                        "avatarurl" => $val["avatarurl"],
                        "gender" => $val["gender"],
                    ]
                );
            }
            $page++;
            usleep(100 * 1000);
        }

    }

}

(new Conversion())->dealWithAll();
<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\logic\prodoct;


use app\common\model\prodoct\Product;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * Product逻辑
 * Class ProductLogic
 * @package app\adminapi\logic\prodoct
 */
class ProductLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            Product::create([
                'title' => $params['title'],
                'desc' => $params['desc'],
                'content' => $params['content'],
                'image' => $params['image'],
                'cover_image' => $params['cover_image'],
                'price' => $params['price'],
                'click_virtual' => $params['click_virtual'],
                'click_actual' => $params['click_actual'],
                'contact' => $params['contact'],
                'url' => $params['url'],
                'public_qrcode' => $params['public_qrcode'],
                'video_qrcode' => $params['video_qrcode'],
                'is_show' => $params['is_show'],
                'sort' => $params['sort']
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 编辑
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            Product::where('id', $params['id'])->update([
                'title' => $params['title'],
                'desc' => $params['desc'],
                'content' => $params['content'],
                'image' => $params['image'],
                'cover_image' => $params['cover_image'],
                'price' => $params['price'],
                'click_virtual' => $params['click_virtual'],
                'click_actual' => $params['click_actual'],
                'contact' => $params['contact'],
                'url' => $params['url'],
                'public_qrcode' => $params['public_qrcode'],
                'video_qrcode' => $params['video_qrcode'],
                'is_show' => $params['is_show'],
                'sort' => $params['sort']
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public static function delete(array $params): bool
    {
        return Product::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public static function detail($params): array
    {
        return Product::findOrEmpty($params['id'])->toArray();
    }
}

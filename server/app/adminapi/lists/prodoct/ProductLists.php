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

namespace app\adminapi\lists\prodoct;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\prodoct\Product;
use app\common\lists\ListsSearchInterface;


/**
 * Product列表
 * Class ProductLists
 * @package app\adminapi\listsprodoct
 */
class ProductLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public function setSearch(): array
    {
        return [
            '=' => ['title', 'is_show'],
        ];
    }


    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public function lists(): array
    {
        return Product::where($this->searchWhere)
            ->field(['id', 'title', 'desc', 'image', 'price', 'click_virtual', 'click_actual', 'contact', 'url', 'public_qrcode', 'video_qrcode', 'is_show', 'sort', 'content', 'cover_image'])
            ->append(['image'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();
    }


    /**
     * @notes 获取数量
     * @return int
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public function count(): int
    {
        return Product::where($this->searchWhere)->count();
    }

}

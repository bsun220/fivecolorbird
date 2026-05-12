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

namespace app\adminapi\validate\prodoct;


use app\common\validate\BaseValidate;


/**
 * Product验证器
 * Class ProductValidate
 * @package app\adminapi\validate\prodoct
 */
class ProductValidate extends BaseValidate
{

     /**
      * 设置校验规则
      * @var string[]
      */
    protected $rule = [
        'id' => 'require',
        'title' => 'require',
        'is_show' => 'require',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'title' => '标题',
        'is_show' => '是否显示:1-是.0-否',
    ];


    /**
     * @notes 添加场景
     * @return ProductValidate
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public function sceneAdd()
    {
        return $this->only(['title','is_show']);
    }


    /**
     * @notes 编辑场景
     * @return ProductValidate
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public function sceneEdit()
    {
        return $this->only(['id','title','is_show']);
    }


    /**
     * @notes 删除场景
     * @return ProductValidate
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return ProductValidate
     * @author likeadmin
     * @date 2025/05/09 14:45
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }

}
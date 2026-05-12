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

namespace app\common\model\activity;


use app\common\model\BaseModel;



/**
 * ActivityTime模型
 * Class ActivityTime
 * @package app\common\model\activity
 */
class ActivityTime extends BaseModel
{
    
    protected $name = 'activity_time';

    /**
     * @notes 关联Reservation
     * @return \think\model\relation\HasMany
     * @author likeadmin
     * @date 2025/05/10 11:38
     */
    public function Reservation()
    {
        return $this->hasMany(\app\common\model\activity\Reservation::class, 'time_slot_id', 'id');
    }
    
}

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

namespace app\common\model\system_msg;


use app\common\model\BaseModel;
use think\model\concern\SoftDelete;


/**
 * SystemMsg模型
 * Class SystemMsg
 * @package app\common\model\system_msg
 */
class SystemMsg extends BaseModel
{
    use SoftDelete;
    protected $name = 'system_msg';
    protected $deleteTime = 'delete_time';

    public function getTypeAttr($value, $data){
        switch ($data['content']){
            case strpos($data['content'], '绩效') !== false:
                return 1;
            case strpos($data['content'], '周报') !== false:
                return 2;
            case strpos($data['content'], '账户') !== false:
                return 3;
        }
    }

}

<?php
namespace app\common\model\performance;

use app\adminapi\logic\ConfigLogic;
use app\common\model\auth\Admin;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Performance extends BaseModel
{
    use SoftDelete;
    protected $name = 'performance';
    protected $deleteTime = 'delete_time';

    public function userInfo()
    {
        return $this->hasOne(Admin::class, 'id', 'user_id');
    }

    public function adminInfo()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

    public function getWorkScoreDescAttr($value, $data)
    {
        $workScoreType = ConfigLogic::getDictByType('work_score_type')['work_score_type'] ?? [];
        $workScoreType = array_column($workScoreType, null, 'value');
        return $workScoreType[$data['work_score']]['name'] ?? '';
    }
}

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

namespace app\adminapi\lists\activity;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\activity\ReservationEnum;
use app\common\model\activity\Activity;
use app\common\model\activity\Reservation;
use app\common\lists\ListsSearchInterface;


/**
 * Reservation列表
 * Class ReservationLists
 * @package app\adminapi\listsactivity
 */
class ReservationLists extends BaseAdminDataLists implements ListsSearchInterface
{

    protected $activity;

    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author likeadmin
     * @date 2025/05/12 10:35
     */
    public function setSearch(): array
    {
        $this->activity = $this->request->get('activity') ?? [];

        return [
//            'between_time' => ['date'],
            '=' => ['status'],
            '%like%' => ['name', 'mobile', 'date'],
        ];
    }


    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author likeadmin
     * @date 2025/05/12 10:35
     */
    public function lists(): array
    {



//        exit();
        $lists = Reservation::withfield(['id', 'user_id', 'date', 'time_slot', 'name', 'mobile', 'num', 'note', 'company', 'status', 'activity_id'])
            ->withJoin(['activity'=>function($query){
                if(!$this->activity) return false;
                $where = [] ;
                $table = (new Activity)->getName();
                foreach ($this->activity as $key => $value){
                    $where[] = ["$table.$key", '=', $value];
                }
                $query->where($where);
            },'user'],"LEFT")
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['status_desc'] = ReservationEnum::getStatusDesc($item['status']);
        }
        return $lists;
    }


    /**
     * @notes 获取数量
     * @return int
     * @author likeadmin
     * @date 2025/05/12 10:35
     */
    public function count(): int
    {
        return Reservation::where($this->searchWhere)->withJoin(['activity'=>function($query){
            if(!$this->activity) return false;
            $where = [] ;
            $table = (new Activity)->getName();
            foreach ($this->activity as $key => $value){
                $where[] = ["$table.$key", '=', $value];
            }
            $query->where($where);
        },'user'],"LEFT")->count();
    }

}

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

namespace app\adminapi\logic\auth;

use app\common\cache\AdminAuthCache;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use app\common\model\auth\AdminDept;
use app\common\model\auth\AdminJobs;
use app\common\model\auth\AdminRole;
use app\common\model\auth\AdminSession;
use app\common\cache\AdminTokenCache;
use app\common\model\auth\SystemRole;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\service\FileService;
use think\facade\Config;
use think\facade\Db;

/**
 * 管理员逻辑
 * Class AdminLogic
 * @package app\adminapi\logic\auth
 */
class AdminLogic extends BaseLogic
{

    /**
     * @notes 添加管理员
     * @param array $params
     * @author 段誉
     * @date 2021/12/29 10:23
     */
    public static function add(array $params)
    {
        Db::startTrans();
        try {

            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);
            $defaultAvatar = config('project.default_image.admin_avatar');
            $avatar = !empty($params['avatar']) ? FileService::setFileUrl($params['avatar']) : $defaultAvatar;



            $admin = Admin::create([
                'name' => $params['name'],
                'account' => $params['account'],
                'avatar' => $avatar,
                'password' => $password,
                'password_mw' => $params['password'],
                'create_time' => time(),
                'disable' => $params['disable'],
                'multipoint_login' => $params['multipoint_login'],

                'age' => $params['age'],
                'email' => $params['email'],
                'card_val' => $params['card_val'],
                'salary' => $params['salary'],
                'address' => $params['address'],
                'entry_time' => $params['entry_time'],
                'sex' => $params['sex'],
                'card' => $params['card'],
                'qualification' => $params['qualification'],
                'mobile' => $params['mobile'],
                'number' => $params['number'],
                'contract_file' => $params['contract_file'],
            ]);

            // 角色
            self::insertRole($admin['id'], $params['role_id'] ?? []);
            // 部门
            self::insertDept($admin['id'], $params['dept_id'] ?? []);
            // 岗位
            self::insertJobs($admin['id'], $params['jobs_id'] ?? []);

            Db::commit();
            return $admin;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 编辑管理员
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2021/12/29 10:43
     */
    public static function edit(array $params): array
    {

        Db::startTrans();
        try {
            $params['file_data'] = array_merge($params['file_data']);
            $params['contract_file'] = array_column($params['file_data'], 'uri');

            // 基础信息
            $data = [
                'id' => $params['id'],
                'name' => $params['name'],
                'account' => $params['account'],
                'disable' => $params['disable'],
                'multipoint_login' => $params['multipoint_login'],

                'age' => $params['age'],
                'email' => $params['email'],
                'card_val' => $params['card_val'],
                'salary' => $params['salary'],
                'address' => $params['address'],
                'entry_time' => $params['entry_time'],
                'sex' => $params['sex'],
                'card' => $params['card'],
                'qualification' => $params['qualification'],
                'mobile' => $params['mobile'],
                'number' => $params['number'],
                'contract_file' => $params['contract_file'],
                'file_data' => array_merge($params['file_data']),
            ];

            // 头像
            $data['avatar'] = !empty($params['avatar']) ? FileService::setFileUrl($params['avatar']) : '';

            // 密码
            if (!empty($params['password'])) {
                $passwordSalt = Config::get('project.unique_identification');
                $data['password'] = create_password($params['password'], $passwordSalt);
                $data['password_mw'] = $params['password'];
            }

            // 禁用或更换角色后.设置token过期
            $roleId = AdminRole::where('admin_id', $params['id'])->column('role_id');
            $editRole = false;
            if (!empty(array_diff_assoc($roleId, $params['role_id']))) {
                $editRole = true;
            }

            if ($params['disable'] == 1 || $editRole) {
                $tokenArr = AdminSession::where('admin_id', $params['id'])->select()->toArray();
                foreach ($tokenArr as $token) {
                    self::expireToken($token['token']);
                }
            }

            Admin::update($data);
            (new AdminAuthCache($params['id']))->clearAuthCache();

            // 删除旧的关联信息
            AdminRole::delByUserId($params['id']);
            AdminDept::delByUserId($params['id']);
            AdminJobs::delByUserId($params['id']);
            // 角色
            self::insertRole($params['id'], $params['role_id']);
            // 部门
            self::insertDept($params['id'], $params['dept_id'] ?? []);
            // 岗位
            self::insertJobs($params['id'], $params['jobs_id'] ?? []);

            Db::commit();
            return $data;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 删除管理员
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2021/12/29 10:45
     */
    public static function delete(array $params): bool
    {
        Db::startTrans();
        try {
            $admin = Admin::findOrEmpty($params['id']);
            if ($admin->root == YesNoEnum::YES) {
                throw new \Exception("超级管理员不允许被删除");
            }
            Admin::destroy($params['id']);
            //设置token过期
            $tokenArr = AdminSession::where('admin_id', $params['id'])->select()->toArray();
            foreach ($tokenArr as $token) {
                self::expireToken($token['token']);
            }
            (new AdminAuthCache($params['id']))->clearAuthCache();

            // 删除旧的关联信息
            AdminRole::delByUserId($params['id']);
            AdminDept::delByUserId($params['id']);
            AdminJobs::delByUserId($params['id']);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function disable(array $params): bool
    {
        Db::startTrans();
        try {
            Admin::whereIn('id', $params['id'])->update(['disable' => 1]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }



    /**
     * @notes 过期token
     * @param $token
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2021/12/29 10:46
     */
    public static function expireToken($token): bool
    {
        $adminSession = AdminSession::where('token', '=', $token)
            ->with('admin')
            ->find();

        if (empty($adminSession)) {
            return false;
        }

        $time = time();
        $adminSession->expire_time = $time;
        $adminSession->update_time = $time;
        $adminSession->save();

        return (new AdminTokenCache())->deleteAdminInfo($token);
    }


    /**
     * @notes 查看管理员详情
     * @param $params
     * @return array
     * @author 段誉
     * @date 2021/12/29 11:07
     */
    public static function detail($params, $action = 'detail'): array
    {

        $admin = Admin::field([
            'id', 'account', 'name', 'disable', 'root',
            'multipoint_login', 'avatar','age', 'email', 'card_val', 'salary', 'address', 'entry_time', 'sex', 'card', 'qualification', 'mobile', 'number', 'contract_file', 'password_mw', 'create_time', 'file_data'
        ])
            ->append(['role_id', 'dept_id', 'jobs_id', 'disable_desc'])
            ->findOrEmpty($params['id'])->toArray();

        // 角色数组（'角色id'=>'角色名称')
        $roleLists = SystemRole::column('name', 'id');
        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');

        $roleName = '';
        if ($admin['root'] == 1) {
            $roleName = '系统管理员';
        } else {
            foreach ($admin['role_id'] as $roleId) {
                $roleName .= $roleLists[$roleId] ?? '';
                $roleName .= '/';
            }
        }

        $deptName = '';
        foreach ($admin['dept_id'] as $deptId) {
            $deptName .= $deptLists[$deptId] ?? '';
            $deptName .= '/';
        }

        $jobsName = '';
        foreach ($admin['jobs_id'] as $jobsId) {
            $jobsName .= $jobsLists[$jobsId] ?? '';
            $jobsName .= '/';
        }

        $admin['role_name'] = trim($roleName, '/');
        $admin['dept_name'] = trim($deptName, '/');
        $admin['jobs_name'] = trim($jobsName, '/');

        if ($action == 'detail') {
            return $admin;
        }

        $result['user'] = $admin;
        // 当前管理员角色拥有的菜单
        $result['menu'] = MenuLogic::getMenuByAdminId($params['id']);
        // 当前管理员橘色拥有的按钮权限
        $result['permissions'] = AuthLogic::getBtnAuthByRoleId($admin);
        return $result;
    }


    public static function count(){
        $count = Db::name('admin')->max('id');
        $count = sprintf("%05d",  $count+1);
        return ['count'=>$count];
    }


    /**
     * @notes 编辑超级管理员
     * @param $params
     * @return Admin
     * @author 段誉
     * @date 2022/4/8 17:54
     */
    public static function editSelf($params)
    {
        $data = [
            'id' => $params['admin_id'],
            'name' => $params['name'],
            'avatar' => FileService::setFileUrl($params['avatar']),
        ];

        if (!empty($params['password'])) {
            $passwordSalt = Config::get('project.unique_identification');
            $data['password'] = create_password($params['password'], $passwordSalt);
        }

        return Admin::update($data);
    }


    /**
     * @notes 新增角色
     * @param $adminId
     * @param $roleIds
     * @throws \Exception
     * @author 段誉
     * @date 2022/11/25 14:23
     */
    public static function insertRole($adminId, $roleIds)
    {
        if (!empty($roleIds)) {
            // 角色
            $roleData = [];
            foreach ($roleIds as $roleId) {
                $roleData[] = [
                    'admin_id' => $adminId,
                    'role_id' => $roleId,
                ];
            }
            (new AdminRole())->saveAll($roleData);
        }
    }


    /**
     * @notes 新增部门
     * @param $adminId
     * @param $deptIds
     * @throws \Exception
     * @author 段誉
     * @date 2022/11/25 14:22
     */
    public static function insertDept($adminId, $deptIds)
    {
        // 部门
        if (!empty($deptIds)) {
            $deptData = [];
            foreach ($deptIds as $deptId) {
                $deptData[] = [
                    'admin_id' => $adminId,
                    'dept_id' => $deptId
                ];
            }
            (new AdminDept())->saveAll($deptData);
        }
    }


    /**
     * @notes 新增岗位
     * @param $adminId
     * @param $jobsIds
     * @throws \Exception
     * @author 段誉
     * @date 2022/11/25 14:22
     */
    public static function insertJobs($adminId, $jobsIds)
    {
        // 岗位
        if (!empty($jobsIds)) {
            $jobsData = [];
            foreach ($jobsIds as $jobsId) {
                $jobsData[] = [
                    'admin_id' => $adminId,
                    'jobs_id' => $jobsId
                ];
            }
            (new AdminJobs())->saveAll($jobsData);
        }
    }


    public static function all($params){
        $field = [
            'id', 'name', 'root', 'number',
        ];

        $adminLists = Admin::field($field)
            ->where([['id','>',1]])
            ->where($params)
            ->append(['role_id', 'dept_id', 'jobs_id'])
            ->select()
            ->toArray();

        // 角色数组（'角色id'=>'角色名称')
        $roleLists = SystemRole::column('name', 'id');
        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');


        //管理员列表增加角色名称
        foreach ($adminLists as $k => $v) {
            $roleName = '';
            if ($v['root'] == 1) {
                $roleName = '系统管理员';
            } else {
                foreach ($v['role_id'] as $roleId) {
                    $roleName .= $roleLists[$roleId] ?? '';
                    $roleName .= '/';
                }
            }

            $deptName = '';
            foreach ($v['dept_id'] as $deptId) {
                $deptName .= $deptLists[$deptId] ?? '';
                $deptName .= '/';
            }

            $jobsName = '';
            foreach ($v['jobs_id'] as $jobsId) {
                $jobsName .= $jobsLists[$jobsId] ?? '';
                $jobsName .= '/';
            }

            $adminLists[$k]['role_name'] = trim($roleName, '/');
            $adminLists[$k]['dept_name'] = trim($deptName, '/');
            $adminLists[$k]['jobs_name'] = trim($jobsName, '/');
        }

        return $adminLists;
    }

}

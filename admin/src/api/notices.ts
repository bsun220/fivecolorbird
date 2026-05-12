import request from '@/utils/request'

// 公告列表
export function apiNoticesLists(params: any) {
    return request.get({ url: '/notices.notices/lists', params })
}

// 添加公告
export function apiNoticesAdd(params: any) {
    return request.post({ url: '/notices.notices/add', params })
}

// 编辑公告
export function apiNoticesEdit(params: any) {
    return request.post({ url: '/notices.notices/edit', params })
}

// 删除公告
export function apiNoticesDelete(params: any) {
    return request.post({ url: '/notices.notices/delete', params })
}

// 公告详情
export function apiNoticesDetail(params: any) {
    return request.get({ url: '/notices.notices/detail', params })
}

// 公告列表
export function apiDeptAllList() {
    return request.get({ url: '/dept.dept/allList'})
}

import request from '@/utils/request'

// 活动表列表
export function apiActivityLists(params: any) {
    return request.get({ url: '/activity.activity/lists', params })
}

// 添加活动表
export function apiActivityAdd(params: any) {
    return request.post({ url: '/activity.activity/add', params })
}

// 编辑活动表
export function apiActivityEdit(params: any) {
    return request.post({ url: '/activity.activity/edit', params })
}

// 删除活动表
export function apiActivityDelete(params: any) {
    return request.post({ url: '/activity.activity/delete', params })
}

// 活动表详情
export function apiActivityDetail(params: any) {
    return request.get({ url: '/activity.activity/detail', params })
}
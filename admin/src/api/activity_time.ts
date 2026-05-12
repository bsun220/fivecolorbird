import request from '@/utils/request'

// 预约时间段关联表列表
export function apiActivityTimeLists(params: any) {
    return request.get({ url: '/activity.activity_time/lists', params })
}

// 添加预约时间段关联表
export function apiActivityTimeAdd(params: any) {
    return request.post({ url: '/activity.activity_time/add', params })
}

// 编辑预约时间段关联表
export function apiActivityTimeEdit(params: any) {
    return request.post({ url: '/activity.activity_time/edit', params })
}

// 删除预约时间段关联表
export function apiActivityTimeDelete(params: any) {
    return request.post({ url: '/activity.activity_time/delete', params })
}

// 预约时间段关联表详情
export function apiActivityTimeDetail(params: any) {
    return request.get({ url: '/activity.activity_time/detail', params })
}
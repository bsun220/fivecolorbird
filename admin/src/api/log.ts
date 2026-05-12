import request from '@/utils/request'

// 操作日志 客户版列表
export function apiLogLists(params: any) {
    return request.get({ url: '/log.log/lists', params })
}

// 添加操作日志 客户版
export function apiLogAdd(params: any) {
    return request.post({ url: '/log.log/add', params })
}

// 编辑操作日志 客户版
export function apiLogEdit(params: any) {
    return request.post({ url: '/log.log/edit', params })
}

// 删除操作日志 客户版
export function apiLogDelete(params: any) {
    return request.post({ url: '/log.log/delete', params })
}

// 操作日志 客户版详情
export function apiLogDetail(params: any) {
    return request.get({ url: '/log.log/detail', params })
}
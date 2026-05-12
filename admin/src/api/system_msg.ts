import request from '@/utils/request'

// 系统消息列表
export function apiSystemMsgLists(params: any) {
    return request.get({ url: '/system_msg.system_msg/lists', params })
}

// 添加系统消息
export function apiSystemMsgAdd(params: any) {
    return request.post({ url: '/system_msg.system_msg/add', params })
}

// 编辑系统消息
export function apiSystemMsgEdit(params: any) {
    return request.post({ url: '/system_msg.system_msg/edit', params })
}

// 删除系统消息
export function apiSystemMsgDelete(params: any) {
    return request.post({ url: '/system_msg.system_msg/delete', params })
}

// 系统消息详情
export function apiSystemMsgDetail(params: any) {
    return request.get({ url: '/system_msg.system_msg/detail', params })
}
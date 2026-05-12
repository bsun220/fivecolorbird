import request from '@/utils/request'

// 预约表列表
export function apiReservationLists(params: any) {
    return request.get({ url: '/activity.reservation/lists', params })
}

// 添加预约表
export function apiReservationAdd(params: any) {
    return request.post({ url: '/activity.reservation/add', params })
}

// 编辑预约表
export function apiReservationEdit(params: any) {
    return request.post({ url: '/activity.reservation/edit', params })
}

// 删除预约表
export function apiReservationDelete(params: any) {
    return request.post({ url: '/activity.reservation/delete', params })
}

// 删除预约表
export function apiReservationVerification(params: any) {
    return request.post({ url: '/activity.reservation/verification', params })
}


// 预约表详情
export function apiReservationDetail(params: any) {
    return request.get({ url: '/activity.reservation/detail', params })
}

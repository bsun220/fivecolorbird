import request from '@/utils/request'

// 产品表列表
export function apiProductLists(params: any) {
    return request.get({ url: '/prodoct.product/lists', params })
}

// 添加产品表
export function apiProductAdd(params: any) {
    return request.post({ url: '/prodoct.product/add', params })
}

// 编辑产品表
export function apiProductEdit(params: any) {
    return request.post({ url: '/prodoct.product/edit', params })
}

// 删除产品表
export function apiProductDelete(params: any) {
    return request.post({ url: '/prodoct.product/delete', params })
}

// 产品表详情
export function apiProductDetail(params: any) {
    return request.get({ url: '/prodoct.product/detail', params })
}
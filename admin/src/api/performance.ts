import request from '@/utils/request'

// 绩效表列表
export function apiPerformanceLists(params: any) {
    return request.get({ url: '/performance.performance/lists', params })
}

// 添加绩效表
export function apiPerformanceAdd(params: any) {
    return request.post({ url: '/performance.performance/add', params })
}

// 编辑绩效表
export function apiPerformanceEdit(params: any) {
    return request.post({ url: '/performance.performance/edit', params })
}

// 删除绩效表
export function apiPerformanceDelete(params: any) {
    return request.post({ url: '/performance.performance/delete', params })
}

// 绩效表详情
export function apiPerformanceDetail(params: any) {
    return request.get({ url: '/performance.performance/detail', params })
}

// 获取周报信息
export function apiWeeklyReportList(params: any) {
    return request.get({ url: '/performance.performance/weeklyReportList', params })
}

import request from '@/utils/request'

// 周报列表
export function apiWeeklyReportLists(params: any) {
    return request.get({ url: '/weekly_report.weekly_report/lists', params })
}

// 添加周报
export function apiWeeklyReportAdd(params: any) {
    return request.post({ url: '/weekly_report.weekly_report/add', params })
}

// 编辑周报
export function apiWeeklyReportEdit(params: any) {
    return request.post({ url: '/weekly_report.weekly_report/edit', params })
}

// 删除周报
export function apiWeeklyReportDelete(params: any) {
    return request.post({ url: '/weekly_report.weekly_report/delete', params })
}

// 周报详情
export function apiWeeklyReportDetail(params: any) {
    return request.get({ url: '/weekly_report.weekly_report/detail', params })
}

// 审批周报
export function apiWeeklyReportExamine(params: any) {
    return request.post({ url: '/weekly_report.weekly_report/examine', params })
}

// 提交周报
export function apiWeeklyReportSubmit(params: any) {
    return request.post({ url: '/weekly_report.weekly_report/submit', params })
}

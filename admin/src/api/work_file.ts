import request from '@/utils/request'

// 表单 工作文件列表
export function apiWorkFileLists(params: any) {
    return request.get({ url: '/work_file.work_file/lists', params })
}

// 添加表单 工作文件
export function apiWorkFileAdd(params: any) {
    return request.post({ url: '/work_file.work_file/add', params })
}

// 编辑表单 工作文件
export function apiWorkFileEdit(params: any) {
    return request.post({ url: '/work_file.work_file/edit', params })
}

// 删除表单 工作文件
export function apiWorkFileDelete(params: any) {
    return request.post({ url: '/work_file.work_file/delete', params })
}

// 表单 工作文件详情
export function apiWorkFileDetail(params: any) {
    return request.get({ url: '/work_file.work_file/detail', params })
}
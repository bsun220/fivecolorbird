<template>
    <div class="edit-container">
        <popup
            ref="popupRef"
            title=""
            :async="true"
            width="1120px"
            :confirm-button-text="false"
            :cancel-button-text="false"
            custom-class="weekly-edit-dialog"
            @confirm="handleSubmit"
            @close="handleClosed"
        >
            <el-form ref="formRef" :model="formData" label-width="0" class="weekly-form-card">
                <!-- 顶部操作区 -->
                <div class="form-header">
                    <div class="form-title">{{ mode === 'add' ? '填写周报' : '编辑周报' }}</div>
                    <div class="flex gap-4">
                        <el-button class="top-btn" @click="closePopup">返回</el-button>
                        <el-button class="top-btn" @click="handleSave">保存</el-button>
                        <el-button class="top-btn" type="primary" @click="handleSubmit">提交</el-button>
                    </div>
                </div>

                <div v-if="Number(formData.status) === 2" class="reject-reply-card">
                    <div class="reject-reply-title">
                        <span class="title-bar"></span>
                        <span class="title-text">审批回复</span>
                    </div>
                    <div class="reject-reply-content">{{ formData.reply || '暂无回复' }}</div>
                </div>

                <!-- 周报时间 -->
                <div class="content-card mb-4">
                    <div class="section-title mb-4">
                        <span class="title-bar"></span>
                        <span class="title-text">周报时间</span>
                    </div>
                    <div class="time-range-row">
                        <span class="time-label">时间范围</span>
                        <div class="time-range-control">
                            <el-date-picker
                                v-model="dateRange"
                                type="daterange"
                                value-format="YYYY-MM-DD"
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期"
                                class="weekly-date-range"
                                @change="handleDateRangeChange"
                            />
                        </div>
                    </div>
                </div>

                <!-- 周报内容 -->
                <div class="content-card mb-4">
                    <div class="section-header mb-4">
                        <div class="section-title">
                            <span class="title-bar"></span>
                            <span class="title-text">周报内容</span>
                        </div>
                        <div class="day-count">已添加 {{ formData.daily_details.length }}/{{ maxAddableDays }} 天</div>
                    </div>

                    <div class="daily-list">
                        <div
                            v-for="(day, dayIndex) in formData.daily_details"
                            :key="dayIndex"
                            class="day-block"
                        >
                            <!-- 星期和合计工时 -->
                            <div class="day-header-row">
                                <div class="day-meta">
                                    <div class="day-index">
                                        <span>日期明细</span>
                                        <small v-if="getDayDate(dayIndex)">{{ getDayDate(dayIndex) }}</small>
                                    </div>
                                    <div class="day-field-group">
                                        <span class="day-field-label">星期</span>
                                        <el-input
                                            :model-value="getDayWeekday(dayIndex)"
                                            disabled
                                            placeholder="自动生成"
                                            class="day-field-control summary-input"
                                        />
                                    </div>
                                    <div class="day-field-group">
                                        <span class="day-field-label">合计工时</span>
                                        <el-input
                                            :model-value="formatHours(getDayTotalHours(day))"
                                            disabled
                                            class="day-field-control summary-input"
                                        />
                                    </div>
                                </div>
                                <el-tooltip
                                    :content="formData.daily_details.length > 1 ? '删除此日期' : '至少保留一个日期'"
                                    placement="top"
                                >
                                    <span>
                                        <el-button
                                            class="day-delete-button"
                                            :disabled="formData.daily_details.length <= 1"
                                            circle
                                            @click="removeDay(dayIndex)"
                                        >
                                            <icon name="el-icon-Delete" />
                                        </el-button>
                                    </span>
                                </el-tooltip>
                            </div>

                            <!-- 工作事项表格 -->
                            <el-table :data="day.tasks" border size="small">
                                <el-table-column label="工作事项" min-width="420">
                                    <template #default="{ row }">
                                        <el-input
                                            v-model="row.name"
                                            type="textarea"
                                            :autosize="{ minRows: 1 }"
                                            resize="none"
                                            class="work-item-input"
                                            placeholder="请填写工作事项"
                                        />
                                    </template>
                                </el-table-column>
                                <el-table-column label="工时（小时）" width="120" align="center">
                                    <template #default="{ row }">
                                        <el-input-number
                                            v-model="row.hours"
                                            :min="0"
                                            :step="0.5"
                                            controls-position="right"
                                            class="!w-full"
                                            @change="syncReportHours"
                                        />
                                    </template>
                                </el-table-column>
                                <el-table-column label="处理结果" min-width="140" align="center">
                                    <template #default="{ row }">
                                        <el-input v-model="row.status" placeholder="请填写" />
                                    </template>
                                </el-table-column>
                                <el-table-column width="80" align="center">
                                    <template #default="{ $index }">
                                        <div class="flex justify-center items-center gap-2">
                                            <el-button
                                                v-if="day.tasks.length > 1"
                                                type="danger"
                                                circle
                                                size="small"
                                                @click="removeTask(dayIndex, $index)"
                                            >
                                                <icon name="el-icon-Minus" />
                                            </el-button>
                                            <el-button
                                                type="primary"
                                                circle
                                                size="small"
                                                @click="addTask(dayIndex)"
                                            >
                                                <icon name="el-icon-Plus" />
                                            </el-button>
                                        </div>
                                    </template>
                                </el-table-column>
                            </el-table>
                        </div>

                        <button
                            type="button"
                            class="add-day-panel"
                            :class="{ 'is-disabled': formData.daily_details.length >= maxAddableDays }"
                            @click="addDay"
                        >
                            <span class="add-day-icon">
                                <icon name="el-icon-Plus" />
                            </span>
                            <span class="add-day-copy">
                                <span class="add-day-title">添加日期</span>
                                <span class="add-day-desc">
                                    {{ formData.daily_details.length >= maxAddableDays ? addDayLimitText : '继续添加下一天的工作明细' }}
                                </span>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- 合计与待办 -->
                <div class="content-card">
                    <div class="summary-row">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">本周工时合计</span>
                            <el-input
                                :model-value="formatHours(reportTotalHours)"
                                disabled
                                class="week-total-input"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600">本周加班工时合计</span>
                            <el-input
                                v-model.number="formData.overtime_hours"
                                placeholder="请填写"
                                style="width: 132px"
                                type="number"
                            />
                        </div>
                    </div>

                    <div class="todo-row">
                        <span class="text-gray-600 todo-label">待办事项</span>
                        <el-input
                            v-model="formData.todo_items"
                            type="textarea"
                            :rows="3"
                            placeholder="请填写下周工作计划..."
                        />
                    </div>
                </div>
            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup name="weeklyReportEdit">
import Popup from '@/components/popup/index.vue'
import {
    apiWeeklyReportAdd,
    apiWeeklyReportDetail,
    apiWeeklyReportEdit,
    apiWeeklyReportSubmit
} from '@/api/weekly_report'
import feedback from '@/utils/feedback'

const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
const emit = defineEmits(['success', 'close'])

const weekdays = ['一', '二', '三', '四', '五', '六', '日']

const createDay = (weekday = '') => ({
    weekday,
    hours: 0,
    tasks: [{ name: '', hours: 0, status: '' }]
})

const parseDateValue = (value: string) => {
    if (!value) return null
    const date = new Date(`${value}T00:00:00`)
    return Number.isNaN(date.getTime()) ? null : date
}

const formatDateValue = (date: Date) => {
    const year = date.getFullYear()
    const month = `${date.getMonth() + 1}`.padStart(2, '0')
    const day = `${date.getDate()}`.padStart(2, '0')
    return `${year}-${month}-${day}`
}

const getDateByOffset = (startValue: string, offset: number) => {
    const start = parseDateValue(startValue)
    if (!start || offset < 0) {
        return ''
    }
    const date = new Date(start)
    date.setDate(start.getDate() + offset)
    return formatDateValue(date)
}

const getWeekdayByDate = (value: string) => {
    const date = parseDateValue(value)
    if (!date) {
        return ''
    }
    const dayIndex = date.getDay()
    return dayIndex === 0 ? '日' : weekdays[dayIndex - 1]
}

const getAutoWeekday = (dayIndex: number) => {
    return getWeekdayByDate(getDateByOffset(formData.start_date, dayIndex))
}

const getDayDate = (dayIndex: number) => {
    return getDateByOffset(formData.start_date, dayIndex)
}

const getDayWeekday = (dayIndex: number) => {
    return getAutoWeekday(dayIndex) || formData.daily_details[dayIndex]?.weekday || ''
}

const syncDailyWeekdays = () => {
    formData.daily_details.forEach((day, dayIndex) => {
        const weekday = getAutoWeekday(dayIndex)
        if (weekday) {
            day.weekday = weekday
        }
    })
}

const selectedDateDays = computed(() => {
    const start = parseDateValue(formData.start_date)
    const end = parseDateValue(formData.end_date)
    if (!start || !end || end.getTime() < start.getTime()) {
        return 0
    }
    const oneDay = 24 * 60 * 60 * 1000
    return Math.floor((end.getTime() - start.getTime()) / oneDay) + 1
})

const maxAddableDays = computed(() => {
    return selectedDateDays.value > 0 ? Math.min(selectedDateDays.value, weekdays.length) : weekdays.length
})

const addDayLimitText = computed(() => {
    if (selectedDateDays.value > 0) {
        return `当前日期范围最多添加 ${maxAddableDays.value} 天`
    }
    return '周一到周日已全部添加'
})

// 初始化每日明细，选择周报时间后自动推算星期。
const initDailyDetails = () => {
    return [createDay()]
}

// 表单数据
const formData = reactive({
    id: '',
    start_date: '',
    end_date: '',
    daily_details: initDailyDetails(),
    total_hours: 0,
    overtime_hours: 0,
    todo_items: '',
    status: 0,
    reply: ''
})

const dateRange = ref<string[]>([])

const getDateRangeDays = (startValue: string, endValue: string) => {
    const start = parseDateValue(startValue)
    const end = parseDateValue(endValue)
    if (!start || !end || end.getTime() < start.getTime()) {
        return 0
    }
    const oneDay = 24 * 60 * 60 * 1000
    return Math.floor((end.getTime() - start.getTime()) / oneDay) + 1
}

const handleDateRangeChange = async (value: string[] | null) => {
    if (!value || value.length !== 2) {
        formData.start_date = ''
        formData.end_date = ''
        return
    }

    const [start, end] = value
    const days = getDateRangeDays(start, end)
    if (days > weekdays.length) {
        await feedback.msgWarning('周报时间最多选择7天')
        dateRange.value = []
        formData.start_date = ''
        formData.end_date = ''
        return
    }

    formData.start_date = start
    formData.end_date = end
    syncDailyWeekdays()
}

// 添加工作事项
const addTask = (dayIndex: number) => {
    formData.daily_details[dayIndex].tasks.push({
        name: '',
        hours: 0,
        status: ''
    })
}

// 删除工作事项
const removeTask = (dayIndex: number, taskIndex: number) => {
    if (formData.daily_details[dayIndex].tasks.length > 1) {
        formData.daily_details[dayIndex].tasks.splice(taskIndex, 1)
        syncReportHours()
    }
}

const addDay = async () => {
    if (formData.daily_details.length >= maxAddableDays.value) {
        await feedback.msgWarning(addDayLimitText.value)
        return
    }
    const nextIndex = formData.daily_details.length
    formData.daily_details.push(createDay(getAutoWeekday(nextIndex)))
}

const removeDay = (dayIndex: number) => {
    if (formData.daily_details.length > 1) {
        formData.daily_details.splice(dayIndex, 1)
        syncDailyWeekdays()
        syncReportHours()
    }
}

const calcTaskHours = (tasks: any[]) => {
    return tasks.reduce((total, task) => {
        const hours = Number(task.hours)
        return total + (Number.isFinite(hours) ? hours : 0)
    }, 0)
}

const getDayTotalHours = (day: any) => {
    return calcTaskHours(day.tasks || [])
}

const reportTotalHours = computed(() => {
    return formData.daily_details.reduce((total, day) => total + getDayTotalHours(day), 0)
})

const formatHours = (value: number) => {
    const hours = Number(value || 0)
    return Number.isInteger(hours) ? `${hours}` : `${Number(hours.toFixed(2))}`
}

const syncReportHours = () => {
    syncDailyWeekdays()
    formData.daily_details.forEach(day => {
        day.hours = getDayTotalHours(day)
    })
    formData.total_hours = reportTotalHours.value
}

const validateDateDetailLimit = async () => {
    if (selectedDateDays.value <= 0) {
        await feedback.msgWarning('请选择有效的日期范围')
        return false
    }
    if (selectedDateDays.value > weekdays.length) {
        await feedback.msgWarning('周报时间最多选择7天')
        return false
    }
    if (formData.daily_details.length > maxAddableDays.value) {
        await feedback.msgWarning(`当前日期范围最多添加 ${maxAddableDays.value} 天，请删除多余日期`)
        return false
    }
    return true
}

const parseJson = (value: any, fallback: any) => {
    if (!value) return fallback
    if (typeof value !== 'string') return value
    try {
        return JSON.parse(value)
    } catch {
        return fallback
    }
}

const normalizeTasks = (tasks: any) => {
    const list = Array.isArray(tasks) && tasks.length ? tasks : [{ name: '', hours: 0, status: '' }]
    return list.map((task: any) => ({
        name: task?.name || '',
        hours: Number(task?.hours || 0),
        status: task?.status || ''
    }))
}

const normalizeDailyDetails = (value: any) => {
    const list = parseJson(value, [])
    if (!Array.isArray(list) || !list.length) {
        return initDailyDetails()
    }
    return list.map((day: any) => ({
        weekday: day?.weekday || '',
        hours: Number(day?.hours || 0),
        tasks: normalizeTasks(day?.tasks)
    }))
}

const normalizeTodoItems = (value: any) => {
    const todo = parseJson(value, value)
    return Array.isArray(todo) ? todo.join('\n') : todo || ''
}

const buildParams = () => ({
    id: formData.id,
    start_date: formData.start_date,
    end_date: formData.end_date,
    daily_details: formData.daily_details,
    total_hours: formData.total_hours,
    overtime_hours: formData.overtime_hours || 0,
    todo_items: formData.todo_items || ''
})

// 打开弹窗
const open = (type = 'add') => {
    mode.value = type
    if (type === 'add') {
        formData.id = ''
        formData.start_date = ''
        formData.end_date = ''
        dateRange.value = []
        formData.daily_details = initDailyDetails()
        formData.total_hours = 0
        formData.overtime_hours = 0
        formData.todo_items = ''
        formData.status = 0
        formData.reply = ''
    }
    popupRef.value?.open()
}

// 设置表单数据
const setFormData = async (data: Record<string, any>) => {
    const detail = data?.id && !data.daily_details
        ? await apiWeeklyReportDetail({ id: data.id })
        : data

    formData.id = detail.id || ''
    formData.start_date = detail.start_date || ''
    formData.end_date = detail.end_date || ''
    dateRange.value = formData.start_date && formData.end_date ? [formData.start_date, formData.end_date] : []
    formData.daily_details = normalizeDailyDetails(detail.daily_details)
    syncReportHours()
    formData.overtime_hours = Number(detail.overtime_hours || 0)
    formData.todo_items = normalizeTodoItems(detail.todo_items)
    formData.status = Number(detail.status || 0)
    formData.reply = detail.reply || ''
}

// 保存
const handleSave = async () => {
    if (!formData.start_date || !formData.end_date) {
        await feedback.msgWarning('请选择日期范围')
        return
    }
    if (!(await validateDateDetailLimit())) {
        return
    }
    syncReportHours()
    try {
        const params = buildParams()
        if (mode.value === 'edit' && formData.id) {
            await apiWeeklyReportEdit(params)
        } else {
            const data = await apiWeeklyReportAdd(params)
            formData.id = data?.id || ''
            mode.value = 'edit'
        }
        await feedback.msgSuccess('保存成功')
        emit('success')
        popupRef.value?.close()
    } catch {
        // error handled by interceptor
    }
}

// 提交
const handleSubmit = async () => {
    if (!formData.start_date || !formData.end_date) {
        await feedback.msgWarning('请选择日期范围')
        return
    }
    if (!(await validateDateDetailLimit())) {
        return
    }
    syncReportHours()
    try {
        let id = formData.id
        if (!id) {
            const data = await apiWeeklyReportAdd(buildParams())
            id = data?.id || ''
            formData.id = id
        } else {
            await apiWeeklyReportEdit(buildParams())
        }
        await apiWeeklyReportSubmit({ id })
        await feedback.msgSuccess('提交成功')
        emit('success')
        popupRef.value?.close()
    } catch {
        // error handled by interceptor
    }
}

// 关闭
const closePopup = () => {
    popupRef.value?.close()
}

const handleClosed = () => {
    emit('close')
}

defineExpose({
    open,
    setFormData
})
</script>

<style scoped>
.content-card {
    background: #fff;
    border-radius: 8px;
    padding: 0;
    margin-bottom: 42px;
}

.weekly-form-card {
    padding: 28px 32px 42px;
    background: #fff;
    border-radius: 8px;
}

.form-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 28px;
    margin-bottom: 28px;
    border-bottom: 1px solid #eee;
}

.form-title {
    color: #333;
    font-size: 24px;
    font-weight: 600;
}

.reject-reply-card {
    margin-bottom: 28px;
    padding: 16px 18px;
    border: 1px solid #ffd6d6;
    border-radius: 8px;
    background: #fff7f7;
}

.reject-reply-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.reject-reply-content {
    color: #606266;
    line-height: 1.7;
    white-space: pre-wrap;
    word-break: break-word;
}

.top-btn {
    min-width: 104px;
    height: 42px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.title-bar {
    width: 4px;
    height: 16px;
    background: #3b42f5;
    border-radius: 2px;
}

.title-text {
    font-size: 15px;
    font-weight: 500;
    color: #303133;
}

.time-range-row {
    display: grid;
    grid-template-columns: 86px minmax(0, 420px);
    align-items: center;
    gap: 12px;
    width: fit-content;
    max-width: 100%;
    padding: 14px 16px;
    margin-left: 52px;
    background: #f8fafc;
    border: 1px solid #ebeef5;
    border-radius: 8px;
}

.time-label {
    flex: 0 0 auto;
    color: #606266;
    font-size: 14px;
    line-height: 34px;
    text-align: right;
}

.time-range-control {
    min-width: 0;
    width: 100%;
}

:deep(.weekly-date-range.el-date-editor) {
    width: 100%;
    height: 34px;
    border-radius: 6px;
    box-shadow: 0 0 0 1px #dcdfe6 inset;
    transition: box-shadow 0.18s ease, background-color 0.18s ease;
}

:deep(.weekly-date-range.el-date-editor:hover),
:deep(.weekly-date-range.el-date-editor.is-active) {
    box-shadow: 0 0 0 1px #3b42f5 inset;
}

:deep(.weekly-date-range .el-range__icon) {
    margin-left: 8px;
    color: #909399;
}

:deep(.weekly-date-range .el-range-input) {
    color: #303133;
    font-size: 14px;
}

:deep(.weekly-date-range .el-range-separator) {
    min-width: 24px;
    color: #909399;
}

.day-count {
    flex: 0 0 auto;
    color: #6b7280;
    font-size: 13px;
    line-height: 28px;
}

.daily-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-left: 52px;
}

.day-block {
    padding: 16px;
    background: #fbfcff;
    border: 1px solid #e8ecf6;
    border-radius: 8px;
    box-shadow: 0 6px 18px rgb(31 41 55 / 4%);
}

.day-header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 14px;
}

.day-meta {
    display: flex;
    flex: 1;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px 24px;
    min-width: 0;
}

.day-index {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
    min-width: 72px;
    color: #303133;
    font-size: 14px;
    font-weight: 600;
}

.day-index small {
    color: #909399;
    font-size: 12px;
    font-weight: 400;
    line-height: 16px;
}

.day-field-group {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.day-field-label {
    flex: 0 0 auto;
    color: #606266;
    font-size: 14px;
}

.day-field-control {
    width: 132px;
}

.summary-input :deep(.el-input__wrapper),
.week-total-input :deep(.el-input__wrapper) {
    background: #f5f7fa;
    box-shadow: 0 0 0 1px #e4e7ed inset;
}

.summary-input :deep(.el-input__inner),
.week-total-input :deep(.el-input__inner) {
    color: #606266;
    -webkit-text-fill-color: #606266;
}

.week-total-input {
    width: 132px;
}

.day-delete-button {
    flex: 0 0 auto;
    color: #f04438;
    background: #fff;
    border-color: #ffd5d2;
}

.day-delete-button:hover,
.day-delete-button:focus {
    color: #fff;
    background: #f04438;
    border-color: #f04438;
}

.day-delete-button.is-disabled,
.day-delete-button.is-disabled:hover,
.day-delete-button.is-disabled:focus {
    color: #c0c4cc;
    background: #f5f7fa;
    border-color: #ebeef5;
}

.add-day-panel {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    min-height: 72px;
    padding: 14px 18px;
    color: #3b42f5;
    background: #f8faff;
    border: 1px dashed #9aa7ff;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.18s ease;
}

.add-day-panel:hover,
.add-day-panel:focus {
    background: #eef2ff;
    border-color: #3b42f5;
    box-shadow: 0 8px 18px rgb(59 66 245 / 10%);
}

.add-day-panel.is-disabled {
    color: #9ca3af;
    background: #f7f8fa;
    border-color: #d8dde8;
    cursor: not-allowed;
    box-shadow: none;
}

.add-day-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    color: #fff;
    background: currentColor;
    border-radius: 50%;
}

.add-day-icon :deep(.el-icon),
.add-day-icon :deep(svg) {
    color: #fff;
}

.add-day-copy {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 2px;
}

.add-day-title {
    color: #303133;
    font-size: 14px;
    font-weight: 600;
}

.add-day-desc {
    color: #6b7280;
    font-size: 12px;
}

.summary-row {
    display: flex;
    gap: 34px;
    margin-left: 0;
}

.todo-row {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-top: 22px;
}

.todo-label {
    flex: 0 0 86px;
    line-height: 34px;
    text-align: right;
}

:deep(.el-input-number) {
    width: 100%;
}

:deep(.el-table .el-input__inner) {
    border: none;
    padding-left: 0;
}

:deep(.work-item-input .el-textarea__inner) {
    min-height: 32px !important;
    padding: 5px 8px;
    line-height: 1.6;
    border: none;
    box-shadow: none;
    resize: none;
    overflow: hidden;
}

:deep(.work-item-input .el-textarea__inner:focus) {
    box-shadow: 0 0 0 1px #dcdfe6 inset;
}

:global(.weekly-edit-dialog .el-dialog__body) {
    padding: 0;
    background: #f6f6f6;
}

:global(.weekly-edit-dialog .el-dialog__header) {
    display: none;
}

:deep(.el-table th.el-table__cell) {
    background: #fff;
    color: #333;
    font-weight: 400;
}

@media (max-width: 768px) {
    .time-range-row {
        grid-template-columns: 1fr;
        width: 100%;
        margin-left: 0;
    }

    .time-label {
        text-align: left;
    }

    :deep(.weekly-date-range.el-date-editor) {
        width: 100%;
    }

    .daily-list {
        margin-left: 0;
    }

    .day-header-row {
        align-items: stretch;
    }

    .day-field-group,
    .day-field-control {
        width: 100%;
    }

    .day-delete-button {
        margin-top: 2px;
    }
}
</style>

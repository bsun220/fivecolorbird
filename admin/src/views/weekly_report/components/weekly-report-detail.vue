<template>
    <div class="weekly-detail">
        <table class="detail-table header-table">
            <colgroup>
                <col class="period-col" />
                <col class="name-col" />
                <col class="number-col" />
                <col class="job-col" />
                <col class="dept-col" />
            </colgroup>
            <tbody>
                <tr>
                    <td class="label-cell period-cell">{{ periodText }}</td>
                    <td class="label-cell name-cell">{{ data.userInfo?.name || '-' }}</td>
                    <td>{{ data.userInfo?.number || '-' }}</td>
                    <td>{{ data.userInfo?.jobs_name || '-' }}</td>
                    <td>{{ data.userInfo?.dept_name || '-' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">
            <span></span>
            周报内容
        </div>

        <div v-for="(day, index) in visibleDays" :key="index" class="day-section">
            <table class="detail-table day-meta-table">
                <colgroup>
                    <col class="side-label-col" />
                    <col class="weekday-col" />
                    <col class="side-label-col" />
                    <col class="weekday-col" />
                </colgroup>
                <tbody>
                    <tr>
                        <td class="label-cell date-cell">{{ getDayDate(index) }}</td>
                        <td class="center-cell">星期{{ getDayWeekday(day, index) }}</td>
                        <td class="label-cell right-cell">合计工时</td>
                        <td>{{ getDayHours(day) }}</td>
                    </tr>
                </tbody>
            </table>
            <table class="detail-table work-table">
                <colgroup>
                    <col class="work-name-col" />
                    <col class="work-hours-col" />
                    <col class="work-result-col" />
                </colgroup>
                <thead>
                    <tr>
                        <th>工作事项</th>
                        <th>工时（小时）</th>
                        <th>处理结果</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(task, taskIndex) in normalizedTasks(day.tasks)" :key="taskIndex">
                        <td>{{ task.name || '-' }}</td>
                        <td>{{ formatHourValue(task.hours) }}</td>
                        <td>{{ task.status || '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="!showAllDays && days.length > visibleDays.length" class="more-dot">⋮</div>

        <table class="detail-table summary-table">
            <colgroup>
                <col class="side-label-col" />
                <col class="weekday-col" />
                <col class="side-label-col" />
                <col class="weekday-col" />
            </colgroup>
            <tbody>
                <tr>
                    <td class="label-cell right-cell">本周合计工时</td>
                    <td>{{ formatHourValue(data.total_hours) }}</td>
                    <td class="label-cell right-cell">本周合计加班工时</td>
                    <td>{{ formatHourValue(data.overtime_hours) }}</td>
                </tr>
                <tr>
                    <td class="label-cell right-cell todo-label">待办事项</td>
                    <td colspan="3" class="todo-cell">{{ todoText }}</td>
                </tr>
            </tbody>
        </table>

        <slot name="after"></slot>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    previewDays: {
        type: Number,
        default: 1
    },
    showAllDays: {
        type: Boolean,
        default: false
    }
})

const parseJson = (value: any, fallback: any) => {
    if (!value) return fallback
    if (typeof value !== 'string') return value
    try {
        return JSON.parse(value)
    } catch {
        return fallback
    }
}

const days = computed(() => {
    const list = parseJson((props.data as any).daily_details, [])
    return Array.isArray(list) ? list : []
})

const visibleDays = computed(() => {
    if (props.showAllDays) {
        return days.value
    }
    return days.value.slice(0, Math.max(props.previewDays, 1))
})

const periodText = computed(() => {
    const start = (props.data as any).start_date || '-'
    const end = (props.data as any).end_date || '-'
    return `${start}—${end}`
})

const parseDateValue = (value: string) => {
    if (!value) return null
    const date = new Date(`${value}T00:00:00`)
    return Number.isNaN(date.getTime()) ? null : date
}

const getDateByOffset = (startValue: string, offset: number) => {
    const start = parseDateValue(startValue)
    if (!start || offset < 0) {
        return null
    }
    const date = new Date(start)
    date.setDate(start.getDate() + offset)
    return date
}

const getWeekdayByDate = (date: Date | null) => {
    if (!date) {
        return ''
    }
    const weekdays = ['日', '一', '二', '三', '四', '五', '六']
    return weekdays[date.getDay()]
}

const formatDateDisplay = (date: Date | null) => {
    if (!date) return ''
    const month = `${date.getMonth() + 1}`.padStart(2, '0')
    const day = `${date.getDate()}`.padStart(2, '0')
    return `${month}-${day}`
}

const getDayDate = (index: number) => {
    return formatDateDisplay(getDateByOffset((props.data as any).start_date, index))
}

const getDayWeekday = (day: any, index: number) => {
    const weekday = getWeekdayByDate(getDateByOffset((props.data as any).start_date, index))
    return weekday || day.weekday || '-'
}

const todoText = computed(() => {
    const rawTodo = (props.data as any).todo_items
    const todo = typeof rawTodo === 'string' ? rawTodo : parseJson(rawTodo, '')
    return Array.isArray(todo) ? todo.join('；') : todo || '暂无'
})

const normalizedTasks = (tasks: any) => {
    return Array.isArray(tasks) && tasks.length ? tasks : [{ name: '', hours: '', status: '' }]
}

const isEmptyHourValue = (value: any) => value === null || value === undefined || value === ''

const toHourNumber = (value: any) => {
    if (isEmptyHourValue(value)) {
        return null
    }
    const hours = Number(value)
    return Number.isFinite(hours) ? hours : null
}

const formatHourValue = (value: any) => {
    const hours = toHourNumber(value)
    if (hours === null) {
        return '-'
    }
    return Number.isInteger(hours) ? `${hours}` : `${Number(hours.toFixed(1))}`
}

const getDayHours = (day: any) => {
    const taskHours = normalizedTasks(day.tasks).reduce((total, task) => {
        return total + (toHourNumber(task.hours) ?? 0)
    }, 0)
    const dayHours = toHourNumber(day.hours)
    if (dayHours !== null && dayHours > 0) {
        return formatHourValue(dayHours)
    }
    return formatHourValue(taskHours)
}
</script>

<style scoped>
.weekly-detail {
    color: #333;
    font-size: 14px;
    line-height: 1.6;
}

.detail-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    background: #fff;
}

.detail-table td,
.detail-table th {
    height: 40px;
    border: 1px solid #e5e7eb;
    padding: 8px 16px;
    font-weight: 400;
    text-align: left;
    vertical-align: middle;
    white-space: pre-wrap;
    word-break: break-word;
}

.detail-table th {
    color: #333;
    background: #fff;
}

.header-table .period-col {
    width: 40%;
}

.header-table .name-col {
    width: 14%;
}

.header-table .number-col {
    width: 14%;
}

.header-table .job-col,
.header-table .dept-col {
    width: 16%;
}

.label-cell {
    background: #eef4ff;
}

.header-table td {
    height: 58px;
    text-align: center;
}

.period-cell {
    font-size: 16px;
    font-weight: 500;
    text-align: center !important;
}

.name-cell {
    font-size: 16px;
    font-weight: 500;
    text-align: center !important;
}

.date-cell {
    width: 19%;
    text-align: center !important;
    font-weight: 500;
    color: #303133;
}

.center-cell {
    text-align: center !important;
}

.right-cell {
    text-align: right !important;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 30px 0 18px;
    font-size: 16px;
    font-weight: 600;
}

.section-title span {
    width: 4px;
    height: 16px;
    border-radius: 2px;
    background: #3430ff;
}

.day-section + .day-section {
    margin-top: 22px;
}

.side-label-col {
    width: 19%;
}

.weekday-col {
    width: 31%;
}

.work-name-col {
    width: 52%;
}

.work-hours-col {
    width: 18%;
}

.work-result-col {
    width: 30%;
}

.day-meta-table td {
    height: 48px;
    border-bottom: none;
}

.work-table th {
    height: 42px;
    color: #333;
    background: #fff;
}

.work-table td {
    min-height: 40px;
}

.more-dot {
    height: 52px;
    color: #999;
    font-size: 30px;
    line-height: 42px;
    text-align: center;
}

.summary-table .todo-label {
    width: 19%;
    height: 118px;
    vertical-align: top;
    padding-top: 16px;
}

.todo-cell {
    height: 118px;
    vertical-align: top;
    padding-top: 16px !important;
    line-height: 1.8;
    white-space: pre-wrap;
    word-break: break-word;
}

.summary-table {
    margin-top: 24px;
}
</style>

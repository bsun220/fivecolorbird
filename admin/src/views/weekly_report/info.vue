<template>
    <div v-loading="loading" class="report-info">
        <weekly-report-detail :data="formData" show-all-days>
            <template #after>
                <div v-if="formData.status === 1 || formData.status === 2" class="section-title">
                    <span></span>
                    审批回复
                </div>
                <table v-if="formData.status === 1 || formData.status === 2" class="reply-table">
                    <tbody>
                        <tr>
                            <td class="reply-label">审批回复</td>
                            <td>{{ formData.reply || '暂无回复' }}</td>
                        </tr>
                    </tbody>
                </table>
            </template>
        </weekly-report-detail>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import { onMounted, reactive, ref } from 'vue'
import { apiWeeklyReportDetail } from '@/api/weekly_report'
import WeeklyReportDetail from './components/weekly-report-detail.vue'

const props = defineProps({
    rowId: {
        type: Number as PropType<number>,
        default: () => null
    }
})

const loading = ref(false)
const formData = reactive({
    id: '',
    start_date: '',
    end_date: '',
    userInfo: {} as Record<string, any>,
    status: 0,
    daily_details: [] as any[],
    total_hours: 0,
    overtime_hours: 0,
    todo_items: '',
    reply: ''
})

const loadDetail = async () => {
    if (!props.rowId) return
    loading.value = true
    try {
        const data = await apiWeeklyReportDetail({ id: props.rowId })
        Object.assign(formData, data)
    } finally {
        loading.value = false
    }
}

onMounted(loadDetail)

defineExpose({
    formData
})
</script>

<style scoped>
.report-info {
    padding: 0 8px;
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

.reply-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.reply-table td {
    height: 120px;
    border: 1px solid #e5e7eb;
    padding: 16px;
    vertical-align: top;
}

.reply-label {
    width: 18%;
    background: #eef4ff;
    text-align: right;
}
</style>

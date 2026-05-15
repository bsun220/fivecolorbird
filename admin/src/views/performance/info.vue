<template>
    <div v-loading="loading" class="performance-detail">
        <section class="detail-section first-section">
            <div class="section-title">
                <span></span>
                基础信息
            </div>
            <table class="detail-table">
                <colgroup>
                    <col class="label-col" />
                    <col class="value-col" />
                    <col class="label-col" />
                    <col class="value-col" />
                </colgroup>
                <tbody>
                    <tr>
                        <td class="label-cell">姓名</td>
                        <td>{{ formData.userInfo?.name || '-' }}</td>
                        <td class="label-cell">工号</td>
                        <td>{{ formData.userInfo?.number || '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">任职岗位</td>
                        <td>{{ formData.userInfo?.jobs_name || '-' }}</td>
                        <td class="label-cell">所属部门</td>
                        <td>{{ formData.userInfo?.dept_name || '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">统计月份</td>
                        <td>{{ statMonth }}</td>
                        <td class="blank-cell"></td>
                        <td class="blank-cell"></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="detail-section">
            <div class="section-title">
                <span></span>
                绩效信息
            </div>
            <table class="detail-table">
                <colgroup>
                    <col class="label-col" />
                    <col class="value-col" />
                    <col class="label-col" />
                    <col class="value-col" />
                </colgroup>
                <tbody>
                    <tr>
                        <td class="label-cell">本月绩效奖金</td>
                        <td>{{ money(formData.merit_pay ?? formData.bonus) }}</td>
                        <td class="label-cell">绩效奖金说明</td>
                        <td>
                            <span class="text-with-link">
                                <span class="ellipsis">{{ formatText(formData.merit_pay_note) }}</span>
                                <el-link
                                    v-if="formData.merit_pay_note"
                                    type="primary"
                                    :underline="false"
                                    @click="openTextDialog('绩效奖金说明', formData.merit_pay_note)"
                                >
                                    查看全部
                                </el-link>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">奖励金额</td>
                        <td>{{ money(formData.reward_amount ?? formData.reward) }}</td>
                        <td class="label-cell">奖励说明</td>
                        <td>
                            <span class="text-with-link">
                                <span class="ellipsis">{{ formatText(formData.reward_amount_note) }}</span>
                                <el-link
                                    v-if="formData.reward_amount_note"
                                    type="primary"
                                    :underline="false"
                                    @click="openTextDialog('奖励说明', formData.reward_amount_note)"
                                >
                                    查看全部
                                </el-link>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">处罚金额</td>
                        <td>{{ money(formData.penalty_amount ?? formData.punishment) }}</td>
                        <td class="label-cell">处罚说明</td>
                        <td>
                            <span class="text-with-link">
                                <span class="ellipsis">{{ formData.penalty_amount_note || '无' }}</span>
                                <el-link
                                    v-if="formData.penalty_amount_note"
                                    type="primary"
                                    :underline="false"
                                    @click="openTextDialog('处罚说明', formData.penalty_amount_note)"
                                >
                                    查看全部
                                </el-link>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">预计发放日期</td>
                        <td>{{ formData.issue_date || '-' }}</td>
                        <td class="label-cell">累计绩效奖金</td>
                        <td>{{ money(formData.cumulative_merit_pay) }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">已发奖金</td>
                        <td>{{ money(formData.issued) }}</td>
                        <td class="label-cell">剩余加班工时</td>
                        <td>{{ remainingOvertimeHours }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="detail-section">
            <div class="section-title">
                <span></span>
                考核信息
            </div>
            <table class="detail-table">
                <colgroup>
                    <col class="label-col" />
                    <col class="value-col" />
                    <col class="label-col" />
                    <col class="value-col" />
                </colgroup>
                <tbody>
                    <tr>
                        <td class="label-cell">本月评分等级</td>
                        <td>{{ assessmentLevel }}</td>
                        <td class="label-cell">本月工作点评</td>
                        <td>
                            <span class="text-with-link">
                                <span class="ellipsis">{{ assessmentComment || '-' }}</span>
                                <el-link
                                    v-if="assessmentComment"
                                    type="primary"
                                    :underline="false"
                                    @click="openTextDialog('本月工作点评', assessmentComment)"
                                >
                                    查看全部
                                </el-link>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="weekly-table">
                <thead>
                    <tr>
                        <th>周报时间</th>
                        <th>实际工时</th>
                        <th>加班工时</th>
                        <th>审批状态</th>
                        <th>审批回复</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in weeklyReports" :key="item.id">
                        <td>{{ formatWeeklyReportPeriod(item) }}</td>
                        <td>{{ formatHours(item.total_hours) }}</td>
                        <td>{{ formatHours(item.overtime_hours) }}</td>
                        <td>
                            <el-tag :type="getWeeklyReportStatusType(item.status)" size="small">
                                {{ getWeeklyReportStatusText(item.status) }}
                            </el-tag>
                        </td>
                        <td>
                            <span class="ellipsis">{{ item.reply || '暂无' }}</span>
                        </td>
                        <td class="weekly-action">
                            <el-link
                                v-perms="['weekly_report.weekly_report/edit']"
                                type="primary"
                                :underline="false"
                                @click="handleReview(item, item.status == 0 ? 'approve' : 'view')"
                            >
                                {{ item.status == 0 ? '审批' : '查看审批' }}
                            </el-link>
                        </td>
                    </tr>
                    <tr v-if="!weeklyReports.length">
                        <td colspan="6" class="empty-cell">暂无数据</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <div class="footer-actions">
            <el-button v-perms="['performance.performance/edit']" size="large" @click="emit('edit', formData)">
                编辑
            </el-button>
            <el-button type="primary" size="large" @click="emit('close')">
                关闭
            </el-button>
        </div>
    </div>

    <el-dialog
        v-model="textDialogVisible"
        :title="textDialog.title"
        width="520px"
        append-to-body
        destroy-on-close
    >
        <div class="dialog-content">{{ textDialog.content || '-' }}</div>
    </el-dialog>

    <el-drawer
        v-model="reviewDrawerVisible"
        :title="reviewTitle"
        size="960px"
        direction="rtl"
        :destroy-on-close="true"
        custom-class="performance-weekly-report-drawer"
        @closed="handleReviewDrawerClosed"
    >
        <WeeklyReportEditPopup
            v-if="reviewDrawerVisible"
            ref="weeklyReportEditRef"
            @success="loadWeeklyReports"
            @close="reviewDrawerVisible = false"
        />
    </el-drawer>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import { computed, onMounted, reactive, ref, nextTick, shallowRef } from 'vue'
import { apiPerformanceDetail, apiWeeklyReportList } from '@/api/performance'
import WeeklyReportEditPopup from '@/views/admin_weekly_report/edit.vue'

const props = defineProps({
    rowId: {
        type: Number as PropType<number>,
        default: () => null
    }
})
const emit = defineEmits<{
    (event: 'close'): void
    (event: 'edit', value: Record<string, any>): void
}>()

const loading = ref(false)
const weeklyReports = ref<any[]>([])
const textDialogVisible = ref(false)
const textDialog = reactive({
    title: '',
    content: ''
})

const weeklyReportEditRef = shallowRef<InstanceType<typeof WeeklyReportEditPopup>>()
const reviewDrawerVisible = ref(false)
const reviewType = ref<'approve' | 'view'>('approve')
const reviewTitle = computed(() => (reviewType.value === 'view' ? '查看审批' : '审批'))

const handleReview = async (row: Record<string, any>, type: 'approve' | 'view') => {
    reviewType.value = type
    reviewDrawerVisible.value = true
    await nextTick()
    weeklyReportEditRef.value?.setFormData(row, reviewType.value)
}

const handleReviewDrawerClosed = () => {
    reviewType.value = 'approve'
}
const formData = reactive<Record<string, any>>({
    id: '',
    userInfo: {},
    remaining_overtime_hours: 0
})

const formatText = (value: any, emptyText = '-') => {
    return value || emptyText
}

const money = (value: any) => {
    const numberValue = Number(value || 0)
    return numberValue.toLocaleString('zh-CN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}

const formatHours = (value: any) => {
    const hours = Number(value)
    if (!Number.isFinite(hours)) {
        return '-'
    }
    return Number.isInteger(hours) ? `${hours}` : `${Number(hours.toFixed(2))}`
}

const remainingOvertimeHours = computed(() => {
    return formatHours(formData.remaining_overtime_hours ?? 0)
})

const formatMonthText = (value: any) => {
    const text = String(value || '')
    const match = text.match(/^(\d{4})-(\d{1,2})/)
    if (!match) return text || '-'
    return `${match[1]}年${Number(match[2])}月`
}

const formatDateText = (value: any) => {
    const text = String(value || '')
    const match = text.match(/^(\d{4})-(\d{1,2})-(\d{1,2})/)
    if (!match) return text || '-'
    return `${match[1]}年${Number(match[2])}月${Number(match[3])}日`
}

const statMonth = computed(() => {
    return formatMonthText(formData.statistical_month || formData.month)
})

const assessmentLevel = computed(() => {
    return formData.work_score_desc || formData.rating || formData.work_score || '-'
})

const assessmentComment = computed(() => {
    return formData.work_comment || formData.comment || ''
})

const openTextDialog = (title: string, content: any) => {
    textDialog.title = title
    textDialog.content = content || '-'
    textDialogVisible.value = true
}

const formatWeeklyReportPeriod = (item: Record<string, any>) => {
    if (!item.start_date || !item.end_date) {
        return '-'
    }
    return `${formatDateText(item.start_date)}-${formatDateText(item.end_date)}`
}

const getWeeklyReportStatusText = (status: any) => {
    const statusMap: Record<string, string> = {
        0: '未审批',
        1: '已审批',
        2: '驳回'
    }
    return statusMap[String(status)] || '未知'
}

const getWeeklyReportStatusType = (status: any) => {
    const typeMap: Record<string, 'warning' | 'success' | 'danger' | 'info'> = {
        0: 'warning',
        1: 'success',
        2: 'danger'
    }
    return typeMap[String(status)] || 'info'
}

const loadWeeklyReports = async () => {
    if (!formData.user_id || !(formData.statistical_month || formData.month)) return
    weeklyReports.value = await apiWeeklyReportList({
        user_id: formData.user_id,
        statistical_month: formData.statistical_month || formData.month
    })
}

const loadingDetail = async () => {
    if (!props.rowId) return
    loading.value = true
    try {
        const data = await apiPerformanceDetail({ id: props.rowId })
        Object.assign(formData, data)
        await loadWeeklyReports()
    } finally {
        loading.value = false
    }
}

onMounted(loadingDetail)

defineExpose({
    formData
})
</script>

<style scoped>
.performance-detail {
    color: #1f2937;
    font-size: 14px;
    line-height: 1.5;
}

.profile-panel {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    gap: 18px;
    padding: 18px 20px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f8fafc;
}

.profile-main {
    display: flex;
    align-items: center;
    min-width: 0;
}

.avatar {
    display: flex;
    flex: 0 0 52px;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border-radius: 50%;
    color: #fff;
    background: #2f6bff;
    font-size: 22px;
    font-weight: 600;
}

.profile-text {
    min-width: 0;
    margin-left: 14px;
}

.profile-name {
    overflow: hidden;
    color: #111827;
    font-size: 20px;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 18px;
    margin-top: 8px;
    color: #6b7280;
}

.profile-extra {
    display: grid;
    grid-template-columns: repeat(2, minmax(110px, 1fr));
    gap: 10px;
    min-width: 260px;
}

.profile-extra-item {
    min-width: 0;
    padding: 10px 12px;
    border: 1px solid #e8eef8;
    border-radius: 6px;
    background: #fff;
}

.profile-extra-item span,
.metric-item span,
.info-label,
.score-block span,
.comment-head span {
    color: #6b7280;
    font-size: 13px;
}

.profile-extra-item strong {
    display: block;
    overflow: hidden;
    margin-top: 4px;
    color: #1f2937;
    font-weight: 500;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.metric-strip {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-top: 14px;
}

.metric-item {
    min-width: 0;
    padding: 14px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
}

.metric-item strong {
    display: block;
    overflow: hidden;
    margin-top: 8px;
    color: #111827;
    font-size: 20px;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.metric-item .is-success {
    color: #16a34a;
}

.metric-item .is-danger {
    color: #dc2626;
}

.detail-section {
    margin-top: 26px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    font-size: 16px;
    font-weight: 600;
}

.section-title-between {
    justify-content: space-between;
}

.section-title-between > div {
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title em {
    color: #6b7280;
    font-size: 13px;
    font-style: normal;
    font-weight: 400;
}

.section-title span {
    display: inline-block;
    width: 4px;
    height: 16px;
    border-radius: 2px;
    background: #2f6bff;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    overflow: hidden;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
}

.info-item {
    display: grid;
    grid-template-columns: 128px minmax(0, 1fr);
    min-width: 0;
    border-right: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
}

.info-item:nth-child(2n) {
    border-right: 0;
}

.info-item:nth-last-child(-n + 1) {
    border-bottom: 0;
}

.info-item-wide {
    grid-column: 1 / -1;
    border-right: 0;
}

.info-label {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    min-height: 44px;
    padding: 10px 14px;
    background: #f3f7ff;
}

.info-value {
    min-width: 0;
    min-height: 44px;
    padding: 10px 14px;
    color: #1f2937;
    word-break: break-word;
}

.info-note {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ellipsis {
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.score-block {
    padding: 16px;
    border-right: 1px solid #e5e7eb;
    background: #f8fafc;
}

.score-block strong {
    display: block;
    overflow: hidden;
    margin-top: 10px;
    color: #111827;
    font-size: 22px;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.comment-block {
    min-width: 0;
    padding: 16px;
}

.comment-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.comment-block p {
    display: -webkit-box;
    overflow: hidden;
    margin: 8px 0 0;
    color: #1f2937;
    line-height: 1.7;
    word-break: break-word;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
}

.report-table {
    width: 100%;
}

.report-table :deep(.el-table__cell) {
    padding: 10px 0;
}

.report-table :deep(.el-table__header th) {
    color: #374151;
    background: #f8fafc;
    font-weight: 600;
}

.table-empty {
    padding: 24px 0;
    color: #9ca3af;
}

.dialog-content {
    max-height: 52vh;
    overflow: auto;
    color: #1f2937;
    line-height: 1.8;
    white-space: pre-wrap;
    word-break: break-word;
}

.performance-detail {
    padding: 0 2px 20px;
    color: #606266;
}

.first-section {
    margin-top: 0;
}

.detail-section {
    margin-top: 28px;
}

.section-title {
    gap: 12px;
    margin-bottom: 22px;
    color: #303133;
    font-size: 18px;
    line-height: 1;
}

.section-title span {
    width: 4px;
    height: 18px;
    background: #3430ff;
}

.detail-table {
    width: 100%;
    overflow: hidden;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    border: 1px solid #dfe3eb;
    border-radius: 4px;
    background: #fff;
}

.label-col {
    width: 19%;
}

.value-col {
    width: 31%;
}

.detail-table td {
    height: 48px;
    border-right: 1px solid #dfe3eb;
    border-bottom: 1px solid #dfe3eb;
    padding: 0 18px;
    color: #606266;
    font-weight: 500;
    vertical-align: middle;
}

.detail-table tr:last-child td {
    border-bottom: 0;
}

.detail-table td:last-child {
    border-right: 0;
}

.detail-table .label-cell {
    color: #4b5563;
    background: #eef4ff;
    text-align: right;
}

.blank-cell {
    background: #fff;
}

.text-with-link {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.text-with-link .ellipsis {
    flex: 1 1 auto;
    max-width: 180px;
}

.weekly-table {
    width: 100%;
    margin-top: 20px;
    overflow: hidden;
    border: 1px solid #ebeef5;
    border-radius: 6px;
    border-collapse: separate;
    border-spacing: 0;
    table-layout: fixed;
    background: #fff;
}

.weekly-table th,
.weekly-table td {
    height: 38px;
    padding: 0 18px;
    color: #606266;
    font-weight: 500;
    text-align: left;
    vertical-align: middle;
}

.weekly-table th {
    height: 42px;
    color: #303133;
    background: #fbfcff;
}

.weekly-table tbody tr + tr td {
    border-top: 1px solid #f0f2f5;
}

.weekly-table th:nth-child(1),
.weekly-table td:nth-child(1) {
    width: 30%;
}

.weekly-table th:nth-child(2),
.weekly-table td:nth-child(2),
.weekly-table th:nth-child(3),
.weekly-table td:nth-child(3),
.weekly-table th:nth-child(4),
.weekly-table td:nth-child(4) {
    width: 12%;
}

.weekly-table th:nth-child(6),
.weekly-table td:nth-child(6) {
    width: 110px;
}

.weekly-action {
    text-align: right !important;
}

.empty-cell {
    height: 64px !important;
    color: #909399 !important;
    text-align: center !important;
}

.footer-actions {
    display: flex;
    justify-content: flex-end;
    gap: 18px;
    margin-top: 22px;
}

.footer-actions :deep(.el-button) {
    min-width: 126px;
    height: 52px;
    border-radius: 8px;
    font-size: 15px;
}

@media (max-width: 680px) {
    .detail-table,
    .weekly-table {
        min-width: 720px;
    }

    .detail-section {
        overflow-x: auto;
    }
}

:global(.performance-info-drawer) {
    max-width: 96vw;
}

:global(.performance-info-drawer .el-drawer__header) {
    margin-bottom: 0;
    padding: 24px 24px 18px;
    color: #303133;
}

:global(.performance-info-drawer .el-drawer__title) {
    font-size: 20px;
    font-weight: 600;
}

:global(.performance-info-drawer .el-drawer__body) {
    padding: 8px 24px 28px;
}

@media (max-width: 980px) {
    .profile-panel {
        grid-template-columns: 1fr;
    }

    .profile-panel {
        flex-direction: column;
    }

    .profile-extra,
    .metric-strip,
    .info-grid {
        grid-template-columns: 1fr;
    }

    .profile-extra {
        min-width: 0;
    }

    .info-item,
    .info-item:nth-child(2n) {
        border-right: 0;
    }

    .score-block {
        border-right: 0;
        border-bottom: 1px solid #e5e7eb;
    }
}

@media (max-width: 560px) {
    .profile-panel {
        padding: 16px;
    }

    .info-item {
        grid-template-columns: 1fr;
    }

    .info-label {
        justify-content: flex-start;
        min-height: auto;
        padding-bottom: 4px;
    }

    .info-value {
        min-height: auto;
        padding-top: 4px;
    }
}

:global(.performance-weekly-report-drawer) {
    width: 960px !important;
    max-width: 96vw;
}

:global(.performance-weekly-report-drawer .el-drawer__header) {
    margin-bottom: 0;
    padding: 26px 24px 16px;
    color: #333;
}

:global(.performance-weekly-report-drawer .el-drawer__title) {
    font-size: 20px;
    font-weight: 600;
}

:global(.performance-weekly-report-drawer .el-drawer__body) {
    padding: 8px 24px 24px;
}

:global(.performance-weekly-report-drawer .el-drawer__footer) {
    padding: 16px 24px 32px;
}

:global(.performance-weekly-report-drawer .weekly-detail) {
    min-width: 0;
}

:global(.performance-weekly-report-drawer .weekly-detail .detail-table td),
:global(.performance-weekly-report-drawer .weekly-detail .detail-table th) {
    padding: 8px 12px;
}

:global(.performance-weekly-report-drawer .weekly-detail .header-table td) {
    height: 52px;
}

:global(.performance-weekly-report-drawer .review-panel) {
    min-height: auto;
}
</style>

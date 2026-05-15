<template>
    <div v-loading="loading" class="review-panel">
        <weekly-report-detail :data="formData" show-all-days>
            <template #after>
                <div class="review-title">
                    <span></span>
                    审批回复
                </div>

                <div v-if="formData.examine_time || formData.adminInfo?.name" class="review-meta">
                    <span v-if="formData.adminInfo?.name">审批人：{{ formData.adminInfo.name }}</span>
                    <span v-if="formData.examine_time">审批时间：{{ formData.examine_time }}</span>
                </div>

                <el-form ref="formRef" :model="formData" :rules="formRules" label-width="90px">
                    <el-form-item label="审批回复" prop="reply" required class="reply-form-item">
                        <el-input
                            v-model="formData.reply"
                            type="textarea"
                            :rows="4"
                            placeholder="请输入审批回复"
                        />
                    </el-form-item>
                </el-form>
            </template>
        </weekly-report-detail>

        <div class="drawer-actions">
            <el-button class="action-btn" @click="handleClose">关闭</el-button>
            <el-button class="action-btn reject-btn" @click="handleReject">驳回</el-button>
            <el-button class="action-btn" type="primary" @click="handlePass">审批通过</el-button>
        </div>
    </div>
</template>

<script lang="ts" setup name="adminWeeklyReportEdit">
import type { FormInstance, FormRules } from 'element-plus'
import { apiWeeklyReportDetail, apiWeeklyReportExamine } from '@/api/weekly_report'
import WeeklyReportDetail from '@/views/weekly_report/components/weekly-report-detail.vue'

const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const loading = ref(false)

const formData = reactive({
    id: '',
    start_date: '',
    end_date: '',
    userInfo: {} as Record<string, any>,
    adminInfo: {} as Record<string, any>,
    status: 1,
    daily_details: [] as any[],
    total_hours: 0,
    overtime_hours: 0,
    todo_items: '',
    reply: '',
    examine_time: ''
})

const formRules: FormRules = {
    reply: [{ required: true, message: '请输入审批回复', trigger: 'blur' }]
}

const setFormData = async (data: Record<any, any>, _type = 'approve') => {
    loading.value = true
    try {
        const detail = data?.id ? await apiWeeklyReportDetail({ id: data.id }) : data
        Object.assign(formData, detail)
    } finally {
        loading.value = false
    }

    if (Number(formData.status) === 0) {
        formData.status = 1
    }
    formData.reply = formData.reply || ''
}

const submitReview = async (status: 1 | 2) => {
    await formRef.value?.validate()
    await apiWeeklyReportExamine({
        id: formData.id,
        status,
        reply: formData.reply
    })
    emit('success')
    emit('close')
}

const handlePass = () => submitReview(1)
const handleReject = () => submitReview(2)

const handleClose = () => {
    emit('close')
}

defineExpose({
    setFormData
})
</script>

<style scoped>
.review-panel {
    display: flex;
    flex-direction: column;
    min-height: 100%;
}

.review-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 30px 0 18px;
    font-size: 16px;
    font-weight: 600;
}

.review-title span {
    width: 4px;
    height: 16px;
    border-radius: 2px;
    background: #3430ff;
}

.review-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 12px;
    color: #666;
}

.reply-form-item {
    margin-bottom: 0;
}

.drawer-actions {
    display: flex;
    justify-content: flex-end;
    gap: 14px;
    margin-top: 16px;
}

.action-btn {
    min-width: 104px;
    height: 42px;
}

.reject-btn {
    color: #ff3b3b;
    border-color: #ff3b3b;
}
</style>

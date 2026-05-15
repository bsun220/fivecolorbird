<style scoped>
.performance-edit-form {
    padding: 2px 4px 0;
}

.performance-edit-form :deep(.el-form-item) {
    margin-bottom: 0;
}

/* 统一所有输入控件高度 —— input / select / date-picker */
.performance-edit-form :deep(.el-input__wrapper),
.performance-edit-form :deep(.el-select__wrapper) {
    min-height: 36px;
    height: 36px;
    box-sizing: border-box;
}

.performance-edit-form :deep(.el-select__wrapper) {
    display: flex;
    align-items: center;
}

.form-section {
    margin-bottom: 28px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.title {
    display: flex;
    align-items: center;
    min-height: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #045dff;
    padding-left: 10px;
    color: #1f2937;
    font-size: 15px;
    font-weight: 600;
    line-height: 1;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 16px 24px;
    margin-bottom: 16px;
}

.form-row:last-child {
    margin-bottom: 0;
}

.form-row > .el-form-item {
    flex: 0 1 calc((100% - 48px) / 3);
    min-width: 0;
}

/* 包含 textarea 的 form-item 填充剩余空间 */
.form-row > .el-form-item:has(.form-textarea) {
    flex: 1 1 0;
}

/* 基础信息区域 —— 固定3列等宽，保证行列对齐 */
.form-row-basic > .el-form-item {
    flex: 0 0 calc((100% - 48px) / 3);
}

.form-input {
    width: 100%;
}

.form-textarea {
    width: 100%;
}

.report-table {
    width: 100%;
    overflow: hidden;
    border: 1px solid var(--el-border-color-lighter);
    border-radius: 6px;
}

@media (max-width: 768px) {
    .form-row > .el-form-item,
    .form-row > .el-form-item:has(.form-textarea) {
        flex: auto;
    }

    .form-textarea {
        min-width: 200px;
    }

    .form-row-basic > .el-form-item {
        flex: auto;
    }
}

:global(.weekly-report-drawer) {
    width: 920px !important;
    max-width: 96vw;
}

:global(.weekly-report-drawer .el-drawer__header) {
    margin-bottom: 0;
    padding: 26px 24px 16px;
    color: #333;
}

:global(.weekly-report-drawer .el-drawer__title) {
    font-size: 20px;
    font-weight: 600;
}

:global(.weekly-report-drawer .el-drawer__body) {
    padding: 8px 24px 10px;
}

:global(.weekly-report-drawer .el-drawer__footer) {
    padding: 16px 24px 32px;
}
</style>

<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="1000px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" class="performance-edit-form" :model="formData" label-width="120px" :rules="formRules">

                <!--基础信息-->
                <div class="form-section">
                    <div class="title">基础信息</div>
                    <!--第一行-->
                    <div class="form-row form-row-basic">
                        <el-form-item label="姓名" prop="user_id">
                            <el-select class="form-input" v-model="formData.user_id" @change="handleUserInfo" clearable placeholder="请选择用户">
                                <el-option
                                    v-for="(item, index) in userList"
                                    :key="index"
                                    :label="item.name"
                                    :value="parseInt(item.id)"
                                />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="工号">
                            <el-input
                                class="form-input"
                                v-model="selectedUser.number"
                                disabled
                                placeholder="工号"
                            />
                        </el-form-item>
                        <el-form-item label="任职岗位">
                            <el-input
                                class="form-input"
                                v-model="selectedUser.jobs_name"
                                disabled
                                placeholder="任职岗位"
                            />
                        </el-form-item>
                    </div>

                    <!--第二行-->
                    <div class="form-row form-row-basic">
                        <el-form-item label="所属部门" >
                            <el-input
                                class="form-input"
                                v-model="selectedUser.dept_name"
                                disabled
                                placeholder="所属部门"
                            />
                        </el-form-item>

                        <el-form-item label="统计月份" prop="statistical_month">
                            <el-date-picker
                                class="form-input !flex"
                                v-model="formData.statistical_month"
                                clearable
                                type="month"
                                value-format="YYYY-MM"
                                placeholder="选择统计月份"
                                @change="handleWeeklyReport"
                            >
                            </el-date-picker>
                        </el-form-item>
                    </div>
                </div>

                <!--绩效信息-->
                <div class="form-section">
                    <div class="title">绩效信息</div>
                    <!--第一行-->
                    <div class="form-row">
                        <el-form-item label="本月绩效奖金" prop="merit_pay">
                            <el-input class="form-input" type="float" v-model="formData.merit_pay" clearable placeholder="请输入绩效工资" />
                        </el-form-item>
                        <el-form-item label="绩效奖金说明" prop="merit_pay_note">
                            <el-input type="textarea" class="form-textarea"
                                      v-model="formData.merit_pay_note"
                                      :rows="1"
                                      clearable placeholder="请输入绩效说明" />
                        </el-form-item>
                    </div>

                    <!--第二行-->
                    <div class="form-row">

                        <el-form-item label="预计发放日期" prop="merit_pay">
                            <el-date-picker
                                class="form-input !flex"
                                v-model="formData.issue_date"
                                clearable
                                type="date"
                                value-format="YYYY-MM-DD"
                                placeholder="选择发放日期"
                            >
                            </el-date-picker>
                        </el-form-item>

                        <el-form-item label="累计绩效奖金" prop="cumulative_merit_pay">
                            <el-input type="float" class="form-input" v-model="formData.cumulative_merit_pay" clearable placeholder="请输入累计绩效工资" />
                        </el-form-item>
                        <el-form-item label="已发奖金" prop="issued">
                            <el-input type="float" class="form-input" v-model="formData.issued" clearable placeholder="请输入已发绩效奖金" />
                        </el-form-item>

                    </div>

                    <!--第三行-->
                    <div class="form-row">
                        <el-form-item label="奖励金额" prop="reward_amount">
                            <el-input type="float" class="form-input" v-model="formData.reward_amount" clearable placeholder="请输入奖励金额" />
                        </el-form-item>
                        <el-form-item label="奖励说明" prop="reward_amount_note">
                            <el-input type="textarea" class="form-textarea"
                                      v-model="formData.reward_amount_note"
                                      :rows="1"
                                      clearable placeholder="请输入奖励金额说明" />
                        </el-form-item>

                    </div>

                    <!--第四行-->
                    <div class="form-row">
                        <el-form-item label="处罚金额" prop="penalty_amount">
                            <el-input type="float" class="form-input" v-model="formData.penalty_amount" clearable placeholder="请输入处罚金额" />
                        </el-form-item>
                        <el-form-item label="处罚说明" prop="penalty_amount_note">
                            <el-input type="textarea" class="form-textarea"
                                      v-model="formData.penalty_amount_note"
                                      :rows="1"
                                      clearable placeholder="请输入处罚金额说明" />
                        </el-form-item>
                    </div>

                    <!--第五行-->
                    <div class="form-row">
                        <el-form-item label="剩余加班工时" prop="remaining_overtime_hours">
                            <el-input type="float" class="form-input" v-model="formData.remaining_overtime_hours" clearable placeholder="请输入剩余加班工时" />
                        </el-form-item>
                    </div>
                </div>


                <!--考核信息-->
                <div class="form-section">
                    <div class="title">考核信息</div>

                    <div class="form-row">
                        <el-form-item label="本月评分等级" prop="work_score">
                            <el-select class="form-input" v-model="formData.work_score" clearable placeholder="请选择评分等级">
                                <el-option
                                    v-for="(item, index) in workScoreOptions"
                                    :key="item.value ?? index"
                                    :label="item.name"
                                    :value="normalizeWorkScoreValue(item.value)"
                                />
                            </el-select>
                        </el-form-item>
                        <el-form-item label="本月工作点评" prop="work_comment">
                            <el-input type="textarea" v-model="formData.work_comment"
                                      class="form-textarea"
                                      :rows="1"
                                      show-word-limit
                                      clearable placeholder="请输入工作点评" />
                        </el-form-item>
                    </div>

                    <div class="report-table">
                    <el-table
                        :data="tableData"
                        style="width: 100%"
                        v-loading="loading"
                    >
                        <el-table-column
                            label="周报时间"
                            width="240">
                            <template #default="{ row }">
                                {{ formatWeeklyReportPeriod(row) }}
                            </template>
                        </el-table-column>
                        <el-table-column
                            label="合计工时"
                            width="180">
                            <template #default="{ row }">
                                {{ row.total_hours ?? row.actual_hours ?? '-' }}
                            </template>
                        </el-table-column>
                        <el-table-column
                            prop="overtime_hours"
                            label="加班工时">
                        </el-table-column>
                        <el-table-column label="审批状态" width="100">
                            <template #default="{ row }">
                                <el-tag v-if="row.status == 0" type="warning">未审批</el-tag>
                                <el-tag v-else-if="row.status == 1" type="success">已审批</el-tag>
                                <el-tag v-else-if="row.status == 2" type="danger">驳回</el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="审批操作" width="120">
                            <template #default="{ row }">
                                <el-button
                                    v-if="row.status == 0"
                                    v-perms="['weekly_report.weekly_report/edit']"
                                    type="primary"
                                    link
                                    @click="handleReview(row, 'approve')"
                                >
                                    审批
                                </el-button>
                                <el-button
                                    v-if="row.status == 1 || row.status == 2"
                                    v-perms="['weekly_report.weekly_report/edit']"
                                    type="primary"
                                    link
                                    @click="handleReview(row, 'view')"
                                >
                                    查看审批
                                </el-button>
                            </template>
                        </el-table-column>
                        <el-table-column
                            label="审批回复"
                            width="180">
                            <template #default="{ row }">
                                {{ row.reply ?? row.remarks ?? '-' }}
                            </template>
                        </el-table-column>
                        <template #empty>
                            <el-empty description="暂无数据" />
                        </template>
                    </el-table>
                    </div>
                </div>



            </el-form>
        </popup>

        <el-drawer
            v-model="reviewDrawerVisible"
            :title="reviewTitle"
            size="920px"
            direction="rtl"
            :destroy-on-close="true"
            custom-class="weekly-report-drawer"
            @closed="handleReviewDrawerClosed"
        >
            <WeeklyReportEditPopup
                v-if="reviewDrawerVisible"
                ref="weeklyReportEditRef"
                @success="getWeeklyReportList"
                @close="reviewDrawerVisible = false"
            />
        </el-drawer>
    </div>
</template>

<script lang="ts" setup name="performanceEdit">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import {apiPerformanceAdd, apiPerformanceEdit, apiPerformanceDetail, apiWeeklyReportList,} from '@/api/performance'
import WeeklyReportEditPopup from '@/views/admin_weekly_report/edit.vue'
import type { PropType } from 'vue'
const Props = defineProps({
    dictData: {
        type: Object as PropType<Record<string, any[]>>,
        default: () => ({})
    },
    userList: {
        type: Array,
        required: true,
        default: () => []
    }
})
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')


// 弹窗标题
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑绩效表' : '新增绩效表'
})

// 定义一个响应式对象来存储选中的用户数据
const selectedUser = reactive({
    id: '',
    number: '',
    jobs_name: '',
    dept_name: '',
});

const statisticalMonth = reactive([]);



const tableData = ref<Array<{
    id?: number;
    node?: string;
    start_date?: string;
    end_date?: string;
    total_hours?: number;
    actual_hours?: number;
    overtime_hours?: number;
    reply?: string;
    remarks?: string;
    status?: number;
}>>([]);
const loading = ref(false)

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

const workScoreOptions = computed(() => Props.dictData?.work_score_type ?? [])

const normalizeWorkScoreValue = (value: any) => {
    if (value === '' || value === null || value === undefined) {
        return ''
    }
    const numberValue = Number(value)
    return Number.isNaN(numberValue) ? value : numberValue
}

const formatWeeklyReportPeriod = (row: Record<string, any>) => {
    if (row.start_date && row.end_date) {
        return `${row.start_date} - ${row.end_date}`
    }
    return row.start_date || row.end_date || row.node || '-'
}

// 表单数据
const formData = reactive({
    id: '',
    user_id: '',
    statistical_month: '',
    merit_pay: '',
    merit_pay_note: '',
    issue_date: '',
    cumulative_merit_pay: '',
    issued: '',
    remaining_overtime_hours: '',
    reward_amount: '',
    reward_amount_note: '',
    penalty_amount: '',
    penalty_amount_note: '',
    work_score: '' as string | number,
    work_comment: '',
})

// 表单验证
const formRules = reactive<any>({
    user_id: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    statistical_month: [{
        required: true,
        message: '请选择统计月份',
        trigger: ['blur']
    }],
    merit_pay: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    merit_pay_note: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    issue_date: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    cumulative_merit_pay: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    issued: [{
        message: '请选择',
        trigger: ['blur']
    }],
    remaining_overtime_hours: [{
        required: true,
        message: '请输入剩余加班工时',
        trigger: ['blur']
    }],
    reward_amount: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    reward_amount_note: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    penalty_amount: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    penalty_amount_note: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
    work_score: [{
        required: true,
        message: '请选择',
        trigger: ['change', 'blur']
    }],
    work_comment: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],

})




const getDetail = async (row: Record<string, any>) => {
    const data = await apiPerformanceDetail({
        id: row.id
    })
    setFormData(data)
}





const getWeeklyReportList = async() => {
    if(statisticalMonth.statistical_month && selectedUser.id){
        const data = await apiWeeklyReportList({
            statistical_month:statisticalMonth.statistical_month,
            user_id:selectedUser.id
        });
        tableData.value = data;
    }
}

const handleWeeklyReport =  async (statistical_month) => {
    statisticalMonth.statistical_month = statistical_month;
    getWeeklyReportList();
}

const handleUserInfo =  async (userId) => {
    const user = Props.userList.find(item => item.id == userId);

    if (user) {
        // 更新 selectedUser 数据
        selectedUser.id = user.id || '';
        selectedUser.number = user.number || '';
        selectedUser.jobs_name = user.jobs_name || '';
        selectedUser.dept_name = user.dept_name || '';

    } else {
        // 清空数据
        selectedUser.id = '';
        selectedUser.number = '';
        selectedUser.jobs_name = '';
        selectedUser.dept_name = '';
    }

    getWeeklyReportList();
}





// 提交按钮
const handleSubmit = async () => {
    await formRef.value?.validate()
    const data = {
        ...formData,
        work_score: normalizeWorkScoreValue(formData.work_score),
    }
    mode.value == 'edit'
        ? await apiPerformanceEdit(data)
        : await apiPerformanceAdd(data)
    popupRef.value?.close()
    emit('success')
}

//打开弹窗
const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
}

// 关闭回调
const handleClose = () => {
    emit('close')
}


// 获取详情
const setFormData = async (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = key === 'work_score'
                ? normalizeWorkScoreValue(data[key])
                : data[key]
        }
    }

    // 编辑时自动回填用户信息（工号、任职岗位、所属部门）
    if (formData.user_id) {
        await handleUserInfo(formData.user_id)
    }

    if(formData.statistical_month && formData.user_id){
        const data = await apiWeeklyReportList({
            statistical_month:formData.statistical_month,
            user_id:formData.user_id
        });
        tableData.value = data;
    }

}

defineExpose({
    open,
    setFormData,
    getDetail
})
</script>

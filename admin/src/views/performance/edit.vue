<style>
.title {border-left: 4px solid #045dff; padding-left: 8px;}
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
            <el-form ref="formRef" :model="formData" label-width="90px" :rules="formRules">

                <!--基础信息-->
                <div class="font-medium mb-7 title">基础信息</div>
                <!--第一行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="用户" prop="user_id">
                        <el-select class="flex-1" style="width: 180px" v-model="formData.user_id" @change="handleUserInfo" clearable placeholder="请选择用户">
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
                            class="flex-1"
                            v-model="selectedUser.number"
                            disabled
                            placeholder="工号"
                            style="width: 180px"
                        />
                    </el-form-item>
                    <el-form-item label="任职岗位">
                        <el-input
                            class="flex-1"
                            v-model="selectedUser.jobs_name"
                            disabled
                            placeholder="任职岗位"
                            style="width: 180px"
                        />
                    </el-form-item>
                </div>

                <!--第二行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="所属部门" >
                        <el-input
                            class="flex-1"
                            v-model="selectedUser.dept_name"
                            disabled
                            placeholder="所属部门"
                            style="width: 180px"
                        />
                    </el-form-item>

                    <el-form-item label="统计月份" prop="statistical_month">
                        <el-date-picker
                            class="flex-1 !flex"
                            v-model="formData.statistical_month"
                            clearable
                            type="month"
                            value-format="YYYY-MM"
                            placeholder="选择统计月份"
                            style="width: 180px"
                            @change="handleWeeklyReport"
                        >
                        </el-date-picker>
                    </el-form-item>
                </div>

                <!--绩效信息-->
                <div class="font-medium mb-7 title">绩效信息</div>
                <!--第一行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="绩效工资" prop="merit_pay">
                        <el-input class="w-[180px]" type="float" v-model="formData.merit_pay" clearable placeholder="请输入绩效工资" />
                    </el-form-item>
                    <el-form-item label="绩效说明" prop="merit_pay_note">
                        <el-input type="textarea" class="w-[500px]"
                                  v-model="formData.merit_pay_note"
                                  :rows="1"
                                  clearable placeholder="请输入绩效说明" />
                    </el-form-item>
                </div>

                <!--第二行-->
                <div class="flex gap-4 mb-4">

                    <el-form-item label="发放日期" prop="issue_date">
                        <el-date-picker
                            class="flex-1 !flex"
                            v-model="formData.issue_date"
                            clearable
                            type="date"
                            value-format="YYYY-MM-DD"
                            placeholder="选择发放日期"
                            style="width: 180px"
                        >
                        </el-date-picker>
                    </el-form-item>

                    <el-form-item label="累计绩效" prop="cumulative_merit_pay">
                        <el-input  type="float" class="w-[180px]" v-model="formData.cumulative_merit_pay" clearable placeholder="请输入累计绩效工资" />
                    </el-form-item>
                    <el-form-item label="已发绩效" prop="issued">
                        <el-input  type="float" class="w-[180px]" v-model="formData.issued" clearable placeholder="请输入已发绩效奖金" />
                    </el-form-item>

                </div>

                <!--第三行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="奖励金额" prop="reward_amount">
                        <el-input  type="float" class="w-[180px]" v-model="formData.reward_amount" clearable placeholder="请输入奖励金额" />
                    </el-form-item>
                    <el-form-item label="奖励说明" prop="reward_amount_note">
                        <el-input type="textarea" class="w-[500px]"
                                  v-model="formData.reward_amount_note"
                                  :rows="1"
                                  clearable placeholder="请输入奖励金额说明" />
                    </el-form-item>

                </div>

                <!--第四行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="处罚金额" prop="penalty_amount">
                        <el-input  type="float" class="w-[180px]" v-model="formData.penalty_amount" clearable placeholder="请输入处罚金额" />
                    </el-form-item>
                    <el-form-item label="处罚说明" prop="penalty_amount_note">
                        <el-input type="textarea" class="w-[500px]"
                                  v-model="formData.penalty_amount_note"
                                  :rows="1"
                                  clearable placeholder="请输入处罚金额说明" />
                    </el-form-item>
                </div>


                <!--基础信息-->
                <div class="font-medium mb-7 title">考核信息</div>

                <div class="flex gap-4 mb-4">
                    <el-form-item label="评分等级" prop="work_score">
                        <el-select class="flex-1" v-model="formData.work_score" clearable placeholder="请选择评分等级"    style="width: 180px">
                            <el-option
                                v-for="(item, index) in dictData.work_score_type"
                                :key="index"
                                :label="item.name"
                                :value="parseInt(item.value)"
                            />
                        </el-select>
                    </el-form-item>

                </div>
                <div class="flex gap-4 mb-4">
                    <el-form-item label="工作点评" prop="work_comment">
                        <el-input type="textarea" v-model="formData.work_comment"
                                  class="w-[500px]"
                                  :rows="1"
                                  show-word-limit
                                  clearable placeholder="请输入工作点评" />
                    </el-form-item>
                </div>

                <div class="flex gap-4 mb-4">
                    <el-table
                        :data="tableData"
                        style="width: 100%"
                        v-loading="loading"
                    >
                        <el-table-column
                            prop="node"
                            label="节点"
                            width="180">
                        </el-table-column>
                        <el-table-column
                            prop="actual_hours"
                            label="实际工时"
                            width="180">
                        </el-table-column>
                        <el-table-column
                            prop="overtime_hours"
                            label="加班工时">
                        </el-table-column>
                        <el-table-column
                            prop="remarks"
                            label="审批回复"
                            width="180">
                        </el-table-column>
                        <template #empty>
                            <el-empty description="暂无数据" />
                        </template>
                    </el-table>
                </div>



            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup name="performanceEdit">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import {apiPerformanceAdd, apiPerformanceEdit, apiPerformanceDetail, apiWeeklyReportList,} from '@/api/performance'
import { timeFormat } from '@/utils/util'
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
    node: string;
    actual_hours: number;
    overtime_hours: number;
    remarks: string;
}>>([]);
const loading = ref(false)

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
    reward_amount: '',
    reward_amount_note: '',
    penalty_amount: '',
    penalty_amount_note: '',
    work_score: '',
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
        required: true,
        message: '请选择',
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
        trigger: ['blur']
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
    const user = Props.userList.find(item => item.id === userId);

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
    const data = { ...formData,  }
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
            formData[key] = data[key]
        }
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

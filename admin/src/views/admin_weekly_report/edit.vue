<style scoped>

table {
    width: 100%;
    border-collapse: collapse; /* 合并边框 */
}

th, td {
    border: 1px solid #ddd; /* 单元格边框 */
    padding: 8px; /* 内边距 */
    text-align: left; /* 文本对齐方式 */
}

th {
    background-color: #f2f2f2; /* 表头背景色 */

}
.c1 {
    background-color: #F0F5FF;
    text-align: left;
    padding-right: 20px;
}
.c2 {
    text-align: left;
    padding-left: 20px;
}

.title {border-left: 4px solid #045dff; padding-left: 8px;}
:deep(.no-wrap-label .el-form-item__label) {
  white-space: nowrap;
}
</style>

<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="700px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="90px" :rules="formRules">
                <table>
                    <tbody>
                    <tr>
                        <td class="c1" style="width: 40%">{{formData.node}}&nbsp;&nbsp;&nbsp;&nbsp;{{formData.userInfo.name}}</td>
                        <td class="c2">{{formData.userInfo.number}}&nbsp;&nbsp;&nbsp;&nbsp;{{formData.userInfo.jobs_name}}&nbsp;&nbsp;&nbsp;&nbsp;{{formData.userInfo.dept_name}}</td>
                    </tr>
                    </tbody>
                </table>
                <div class="font-medium mb-7"></div>
                <div class="font-medium mb-7 title">工时统计</div>

                <div class="flex gap-2 mb-2">
                    <el-form-item label="本周预计工时" prop="working_hours" class="no-wrap-label">
                        <el-input
                            type="float"
                            v-model="formData.working_hours"
                            clearable
                        />
                    </el-form-item>

                    <el-form-item label="实际工时" prop="actual_hours">
                        <el-input
                            type="float"
                            v-model="formData.actual_hours"
                            clearable
                        />
                    </el-form-item>
                </div>


                <div class="flex gap-2 mb-2">
                    <el-form-item label="未完成工时" prop="unfinished_work_hours" class="no-wrap-label">
                        <el-input
                            type="float"
                            v-model="formData.unfinished_work_hours"
                            clearable
                        />
                    </el-form-item>

                    <el-form-item label="加班工时" prop="overtime_hours">
                        <el-input
                            type="float"
                            v-model="formData.overtime_hours"
                            clearable
                        />
                    </el-form-item>

                </div>


                <div class="font-medium mb-7"></div>
                <div class="font-medium mb-7 title">审批</div>

                <!-- 多处登录 -->
                <el-form-item label="审核状态">
                    <div>
                        <el-switch
                            v-model="formData.status"
                            :active-value="2"
                            :inactive-value="1"
                        />
                        <div class="form-tips">是否通过</div>
                    </div>
                </el-form-item>

                <el-form-item label="审批回复" prop="remarks">
                    <el-input
                        v-model="formData.remarks"
                        type="textarea"
                        :rows="2"
                        maxlength="30"
                        show-word-limit
                    />
                </el-form-item>



            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup name="weeklyReportEdit">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import {
    apiWeeklyReportAdd,
    apiWeeklyReportEdit,
    apiWeeklyReportDetail,
    apiWeeklyReportExamine
} from '@/api/weekly_report'
import { timeFormat } from '@/utils/util'
import type { PropType } from 'vue'
import {fileInfo} from "@/api/file";
const props = defineProps({
    dictData: {
        type: Object as PropType<Record<string, any[]>>,
        default: () => ({})
    }
})
const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')


// 弹窗标题
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '审批' : ''
})

// 表单数据
const formData = reactive({
    id: '',
    userInfo:[],
    node:'',
    working_hours : '',
    actual_hours : '',
    unfinished_work_hours : '',
    overtime_hours : '',
    remarks : '',
    status : '',

})


// 通用的浮点数验证函数
const validateFloat = (rule, value, callback) => {
    if (value === "" || value == null) {
        callback(new Error("请输入有效数字"));
        return;
    }

    // 检查是否为数字
    if (isNaN(Number(value))) {
        callback(new Error("请输入数字（如 8 或 8.5）"));
        return;
    }

    // 检查是否为浮点数（允许整数）
    const num = parseFloat(value);
    if (Number.isNaN(num)) {
        callback(new Error("请输入有效数字"));
        return;
    }

    // 检查小数位数（可选）
    const decimalPart = String(value).split(".")[1];
    if (decimalPart && decimalPart.length > 2) {
        callback(new Error("最多保留2位小数"));
        return;
    }

    // 检查数值范围（可选）
    if (num < 0) {
        callback(new Error("工时不能为负数"));
        return;
    }

    callback(); // 验证通过
};

// 表单验证
const formRules = reactive<any>({
    working_hours: [
        {
            required: true,
            message: '请输入本周预计工时',
            trigger: ['blur']
        },
        {
            validator:validateFloat,
            trigger: ["blur", "change"]
        }
    ],
    actual_hours: [
        {
            required: true,
            message: '请输入实际工时',
            trigger: ['blur']
        },
        {
            validator:validateFloat,
            trigger: ["blur", "change"]
        }
    ],
    unfinished_work_hours: [
        {
            required: true,
            message: '请输入未完成工时',
            trigger: ['blur']
        },
        {
            validator:validateFloat,
            trigger: ["blur", "change"]
        }
    ],
    overtime_hours: [
        {
            required: true,
            message: '请输入加班工时',
            trigger: ['blur']
        },
        {
            validator:validateFloat,
            trigger: ["blur", "change"]
        }
    ],
    remarks : [
        {
            required: true,
            message: '请输入审核备注',
            trigger: ['blur']
        }
    ],

})


// 获取详情
const setFormData = async (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = data[key]
        }
    }
}

const getDetail = async (row: Record<string, any>) => {
    const data = await apiWeeklyReportDetail({
        id: row.id
    })
    setFormData(data)
}






// 提交按钮
const handleSubmit = async () => {
    await formRef.value?.validate()
    const data = { ...formData,  }
    mode.value == 'edit' 
        ? await apiWeeklyReportExamine(data)
        : await apiWeeklyReportAdd(data)
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



defineExpose({
    open,
    setFormData,
    getDetail
})
</script>

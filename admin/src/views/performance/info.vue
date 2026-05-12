<style scoped>
/* 文本截断样式 */
.truncate-text {
    display: inline-block;
    max-width: calc(100% - 40px);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: middle;
}

/* 查看链接样式 */
.view-link {
    font-size: 12px;
    padding: 0 4px;
    margin-left: 4px;
}

/* Popover内容区域样式 */
:deep(.reward-note-popover) {
    word-break: break-word;
}

/* 表格样式优化 */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 12px 16px;
    text-align: left;
    vertical-align: middle;
    line-height: 1.5;
}

th {
    background-color: #f2f2f2;
}

/* 表格单元格样式优化 */
.c1 {
    background-color: #F0F5FF;
    text-align: right;
    padding-right: 20px;
    font-weight: 500;
    width: 20%;
}

.c2 {
    text-align: left;
    padding-left: 20px;
    width: 30%;
}

/* 标题样式 */
.title {
    border-left: 4px solid #045dff;
    padding-left: 12px;
    font-size: 16px;
    font-weight: 600;
    margin: 24px 0 16px 0;
}

/* 表格行悬停效果 */
table tbody tr:hover {
    background-color: #f9f9f9;
}

/* Element Plus 表格样式优化 */
:deep(.el-table) {
    border: 1px solid #ddd;
    border-radius: 4px;
}

:deep(.el-table th) {
    background-color: #f2f2f2;
    font-weight: 600;
}

:deep(.el-table td) {
    padding: 12px 0;
}

:deep(.el-table--border) {
    border: 1px solid #ddd;
}
</style>
<template>
    <div class="font-medium mb-7 title">基础信息</div>
    <table style="width: 100%; margin-bottom: 20px;">
        <tbody>
        <tr>
            <td class="c1">姓名</td>
            <td class="c2">{{formData.userInfo?.name || '-'}}</td>
            <td class="c1">工号</td>
            <td class="c2">{{formData.userInfo?.number || '-'}}</td>
        </tr>
        <tr>
            <td class="c1">任职岗位</td>
            <td class="c2">{{formData.userInfo?.jobs_name || '-'}}</td>
            <td class="c1">所属部门</td>
            <td class="c2">{{formData.userInfo?.dept_name || '-'}}</td>
        </tr>
        <tr>
            <td class="c1">统计月份</td>
            <td class="c2">{{formData.statistical_month || '-'}}</td>
            <td class="c1"></td>
            <td class="c2"></td>
        </tr>
        </tbody>
    </table>

    <div class="font-medium mb-7 title">绩效信息</div>
    <table style="width: 100%; margin-bottom: 20px;">
        <tbody>
            <tr>
                <td class="c1">本月绩效奖金</td>
                <td class="c2">{{formData.merit_pay || '-'}}</td>
                <td class="c1">绩效奖励说明</td>
                <td class="c2">
                    <template v-if="formData.merit_pay_note">
                        <div class="flex items-center">
                            <span class="truncate-text mr-2">
                            {{ formData.merit_pay_note.length > 6 ? formData.merit_pay_note.substring(0, 6) + '...' : formData.merit_pay_note }}
                            </span>
                            <el-popover
                                placement="bottom-start"
                                :width="Math.min(400, formData.merit_pay_note.length * 10 + 50)"
                                trigger="click"
                                popper-class="reward-note-popover"
                            >
                                <template #reference>
                                    <el-link
                                        type="primary"
                                        :underline="false"
                                        class="view-link"
                                        v-show="formData.merit_pay_note.length > 6"
                                    >
                                        查看
                                    </el-link>
                                </template>
                                <div class="p-2 max-h-[300px] overflow-auto">
                                    <pre class="whitespace-pre-wrap font-sans">{{ formData.merit_pay_note }}</pre>
                                </div>
                            </el-popover>
                        </div>
                    </template>
                    <span v-else>-</span>
                </td>
            </tr>
            <tr>
                <td class="c1">奖励金额</td>
                <td class="c2">{{formData.reward_amount || '-'}}</td>
                <td class="c1">奖励说明</td>
                <td class="c2">
                    <template v-if="formData.reward_amount_note">
                        <div class="flex items-center">
                            <span class="truncate-text mr-2">
                            {{ formData.reward_amount_note.length > 6 ? formData.reward_amount_note.substring(0, 6) + '...' : formData.reward_amount_note }}
                            </span>
                            <el-popover
                                placement="bottom-start"
                                :width="Math.min(400, formData.reward_amount_note.length * 10 + 50)"
                                trigger="click"
                                popper-class="reward-note-popover"
                            >
                                <template #reference>
                                    <el-link
                                        type="primary"
                                        :underline="false"
                                        class="view-link"
                                        v-show="formData.reward_amount_note.length > 6"
                                    >
                                        查看
                                    </el-link>
                                </template>
                                <div class="p-2 max-h-[300px] overflow-auto">
                                    <pre class="whitespace-pre-wrap font-sans">{{ formData.reward_amount_note }}</pre>
                                </div>
                            </el-popover>
                        </div>
                    </template>
                    <span v-else>-</span>
                </td>
            </tr>
            <tr>
                <td class="c1">处罚金额</td>
                <td class="c2">{{formData.penalty_amount || '-'}}</td>
                <td class="c1">处罚说明</td>
                <td class="c2">
                    <template v-if="formData.penalty_amount_note">
                        <div class="flex items-center">
                            <span class="truncate-text mr-2">
                            {{ formData.penalty_amount_note.length > 6 ? formData.penalty_amount_note.substring(0, 6) + '...' : formData.penalty_amount_note }}
                            </span>
                            <el-popover
                                placement="bottom-start"
                                :width="Math.min(400, formData.penalty_amount_note.length * 10 + 50)"
                                trigger="click"
                                popper-class="reward-note-popover"
                            >
                                <template #reference>
                                    <el-link
                                        type="primary"
                                        :underline="false"
                                        class="view-link"
                                        v-show="formData.penalty_amount_note.length > 6"
                                    >
                                        查看
                                    </el-link>
                                </template>
                                <div class="p-2 max-h-[300px] overflow-auto">
                                    <pre class="whitespace-pre-wrap font-sans">{{ formData.penalty_amount_note }}</pre>
                                </div>
                            </el-popover>
                        </div>
                    </template>
                    <span v-else>-</span>
                </td>
            </tr>
            <tr>
                <td class="c1">预计发放日期</td>
                <td class="c2">{{formData.issue_date || '-'}}</td>
                <td class="c1">累计绩效奖金</td>
                <td class="c2">{{formData.cumulative_merit_pay || '-'}}</td>
            </tr>
            <tr>
                <td class="c1">已发奖金</td>
                <td class="c2">{{formData.issued || '-'}}</td>
                <td class="c1"></td>
                <td class="c2"></td>
            </tr>
        </tbody>
    </table>

    <div class="font-medium mb-7 title">考核信息</div>
    <table style="width: 100%; margin-bottom: 20px;">
        <tbody>
        <tr>
            <td class="c1">本月评分等级</td>
            <td class="c2">
                <template v-for="(item, index) in dictData.work_score_type">
                    <span v-if="item.value == formData.work_score">{{ item.name }}</span>
                </template>
            </td>
            <td class="c1">本月工作点评</td>
            <td class="c2">
                <template v-if="formData.work_comment">
                    <div class="flex items-center">
                        <span class="truncate-text mr-2">
                            {{ formData.work_comment.length > 6 ? formData.work_comment.substring(0, 6) + '...' : formData.work_comment }}
                        </span>
                        <el-popover
                            placement="bottom-start"
                            :width="Math.min(400, formData.work_comment.length * 10 + 50)"
                            trigger="click"
                            popper-class="reward-note-popover"
                        >
                            <template #reference>
                                <el-link
                                    type="primary"
                                    :underline="false"
                                    class="view-link"
                                    v-show="formData.work_comment.length > 6"
                                >
                                    查看
                                </el-link>
                            </template>
                            <div class="p-2 max-h-[300px] overflow-auto">
                                <pre class="whitespace-pre-wrap font-sans">{{ formData.work_comment }}</pre>
                            </div>
                        </el-popover>
                    </div>
                </template>
                <span v-else>-</span>
            </td>
        </tr>
        </tbody>
    </table>

    <el-table
        :data="tableData"
        style="width: 100%; margin-bottom: 20px;"
        v-loading="loading2"
        border
        stripe
    >
        <el-table-column
            prop="node"
            label="节点"
            width="200"
            align="center">
        </el-table-column>
        <el-table-column
            prop="actual_hours"
            label="实际工时"
            width="200"
            align="center">
        </el-table-column>
        <el-table-column
            prop="overtime_hours"
            label="加班工时"
            width="200"
            align="center">
        </el-table-column>
        <el-table-column
            prop="remarks"
            label="审批回复"
            width="200"
            align="center">
            <template #default="{row}">
                <div class="flex items-center justify-center">
                    <span class="truncate-text mr-2">
                        {{ row.remarks.length > 6 ? row.remarks.substring(0, 6) + '...' : row.remarks }}
                    </span>
                    <el-popover
                        placement="bottom-start"
                        :width="Math.min(400, row.remarks.length * 10 + 50)"
                        trigger="click"
                        popper-class="reward-note-popover"
                    >
                        <template #reference>
                            <el-link
                                type="primary"
                                :underline="false"
                                class="view-link"
                                v-show="row.remarks.length > 6"
                            >
                                查看
                            </el-link>
                        </template>
                        <div class="p-2 max-h-[300px] overflow-auto">
                            <pre class="whitespace-pre-wrap font-sans">{{ row.remarks }}</pre>
                        </div>
                    </el-popover>
                </div>
            </template>
        </el-table-column>
        <template #empty>
            <el-empty description="暂无数据" />
        </template>
    </el-table>
</template>

<script lang="ts" setup>
import type { FormInstance } from 'element-plus'
import type { PropType } from 'vue'
import {onMounted, ref, reactive} from "vue";
import {apiPerformanceDetail, apiWeeklyReportList} from "@/api/performance";

const props = defineProps({
    data: {
        type: Object as PropType<Record<string, any>>,
        default: () => ({})
    },
    rowId: {
        type: Number,
        default: 0
    },
    dictData: {
        type: Object as PropType<Record<string, any[]>>,
        default: () => ({})
    },
})

const formRef = ref<FormInstance>()
const formData = reactive({
    id: '',
    userInfo: {} as Record<string, any>,
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
    start_time: '',
    end_time: ''
})

const tableData = ref<Array<{
    node: string;
    actual_hours: number;
    overtime_hours: number;
    remarks: string;
}>>([]);
const loading2 = ref(false)

const loading = async () => {
    const data = await apiPerformanceDetail({
        id: props.rowId
    })
    // 正确遍历对象并赋值到formData
    Object.keys(data).forEach(key => {
        if (key in formData) {
            (formData as any)[key] = data[key];
        }
    });

    if(formData.statistical_month && formData.user_id){
        const data = await apiWeeklyReportList({
            statistical_month:formData.statistical_month,
            user_id:formData.user_id
        });
        tableData.value = data;
    }
}

onMounted(() => {
    loading()
})

defineExpose({
    formRef,
    formData,
})
</script>

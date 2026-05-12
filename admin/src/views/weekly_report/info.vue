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
    text-align: right;
    padding-right: 20px;
}
.c2 {
    text-align: left;
    padding-left: 20px;
}

.title {border-left: 4px solid #045dff; padding-left: 8px;}

/* 防止标签换行 */
:deep(.no-wrap-label .el-form-item__label) {
    white-space: nowrap;
    min-width: 90px;
}
</style>
<template>
<!--    <div class="font-medium mb-7 title">基础信息</div>-->
    <table>
        <tbody>
        <tr>
            <td class="c1" style="width: 50%"><b>{{formData.node}}&nbsp;&nbsp;&nbsp;&nbsp;{{formData.userInfo.name}}</b></td>
            <td class="c2" style="width: 50%">{{formData.userInfo.number}}&nbsp;&nbsp;&nbsp;&nbsp;{{formData.userInfo.jobs_name}}&nbsp;&nbsp;&nbsp;&nbsp;{{formData.userInfo.dept_name}}</td>
        </tr>
        </tbody>
    </table>
    <div class="font-medium mb-7"></div>
    <div class="font-medium mb-7 title">工时统计</div>
    <table>
        <tbody>
            <tr>
                <td class="c1" style="width: 20%">本周预计工时</td>
                <td class="c2" style="width: 30%">{{formData.working_hours}}</td>
                <td class="c1" style="width: 20%">实际工时</td>
                <td class="c2" style="width: 30%">{{formData.actual_hours}}</td>
            </tr>
            <tr>
                <td class="c1" style="width: 20%">未完成工时</td>
                <td class="c2" style="width: 30%">{{formData.unfinished_work_hours}}</td>
                <td class="c1" style="width: 20%">加班工时</td>
                <td class="c2" style="width: 30%">{{formData.overtime_hours}}</td>
            </tr>
        </tbody>
    </table>
    <div class="font-medium mb-7"></div>
    <div class="font-medium mb-7 title">审批回复</div>
    <table>
        <tbody>
        <tr>
            <td class="c1" style="width: 20%">审批回复</td>
            <td class="c2 col-span-3" colspan="3" >{{formData.remarks}}</td>
        </tr>
        </tbody>
    </table>
</template>

<script lang="ts" setup>
import type { FormInstance } from 'element-plus'

import type { PropType } from 'vue'
import {onMounted, ref, watch} from "vue";
import { apiWeeklyReportDetail } from '@/api/weekly_report'

const props = defineProps({
    data: {
        type: Object as PropType<Record<string, any>>,
        default: () => ({})
    },
    rowId: {
        type: Number as PropType<Record<string, any>>,
        default: () => ({})
    },
})

const formRef = ref<FormInstance>()
const formData = reactive({
    id: '',
    userInfo: [],
    node: '',
    working_hours: 0,
    actual_hours: 0,
    unfinished_work_hours: 0,
    overtime_hours: 0,
    remarks: '',
})



const loading = async () => {
    const data = await apiWeeklyReportDetail({
        id: props.rowId
    })
    // 正确遍历对象并赋值到formData
    Object.keys(data).forEach(key => {
        formData[key] = data[key];
    });

}

onMounted(() => {
    loading()
})


defineExpose({
    formRef,
    formData,
})

</script>

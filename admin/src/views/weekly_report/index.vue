<template>
    <div>
        <el-card class="!border-none mb-4" shadow="never">
            <el-form
                class="mb-[-16px]"
                :model="queryParams"
                inline
            >
                <el-form-item label="日期范围" prop="date_range">
                    <daterange-picker
                        v-model:startTime="queryParams.start_date"
                        v-model:endTime="queryParams.end_date"
                        type="daterange"
                        range-separator="至"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                    />
                </el-form-item>
                <el-form-item label="审核状态" prop="status">
                    <el-select
                        v-model="queryParams.status"
                        class="status-select"
                        placeholder="请选择状态"
                        clearable
                    >
                        <el-option label="全部" value=""></el-option>
                        <el-option label="未审批" :value="0" />
                        <el-option label="已审批" :value="1" />
                        <el-option label="驳回" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none" v-loading="pager.loading" shadow="never">
            <el-button v-perms="['weekly_report.weekly_report/add']" type="primary" @click="handleAdd">
                <template #icon>
                    <icon name="el-icon-Plus" />
                </template>
                填写周报
            </el-button>
            <div class="mt-4">
                <el-table :data="pager.lists" @selection-change="handleSelectionChange">
                    <el-table-column label="日期范围" min-width="220" show-overflow-tooltip>
                        <template #default="{ row }">
                            {{ row.start_date }} ~ {{ row.end_date }}
                        </template>
                    </el-table-column>

                    <el-table-column label="总工时" prop="total_hours" show-overflow-tooltip>
                        <template #default="{ row }">
                            {{ formatHoursDisplay(row.total_hours) }}
                        </template>
                    </el-table-column>

                    <el-table-column label="加班工时" prop="overtime_hours" show-overflow-tooltip>
                        <template #default="{ row }">
                            {{ formatHoursDisplay(row.overtime_hours) }}
                        </template>
                    </el-table-column>

                    <el-table-column label="审核状态" prop="status">
                        <template #default="{ row }">
                            <el-tag v-if="row.status == 0 && !row.submit_time" type="info">未提交</el-tag>
                            <el-tag v-else-if="row.status == 0" type="warning">未审批</el-tag>
                            <el-tag v-else-if="row.status == 1" type="success">已审批</el-tag>
                            <el-tag v-else-if="row.status == 2" type="danger">驳回</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column label="审批回复" min-width="180" show-overflow-tooltip>
                        <template #default="{ row }">
                            <span>{{ row.status == 1 || row.status == 2 ? row.reply || '暂无回复' : '-' }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="上传时间" prop="create_time" min-width="180" />

                    <el-table-column label="操作" width="280" fixed="right">
                        <template #default="{ row }">
                            <el-button
                                type="primary"
                                link
                                @click="handleInfo(row)"
                            >
                                查看
                            </el-button>
                            <el-button
                                v-if="(row.status == 0 && !row.submit_time) || row.status == 2"
                                v-perms="['weekly_report.weekly_report/edit']"
                                type="primary"
                                link
                                @click="handleEdit(row)"
                            >
                                编辑
                            </el-button>
                            <el-button
                                v-if="row.status == 0 && !row.submit_time"
                                v-perms="['weekly_report.weekly_report/delete']"
                                type="danger"
                                link
                                @click="handleDelete(row.id)"
                            >
                                删除
                            </el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex mt-4 justify-end">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <edit-popup v-if="showEdit" ref="editRef" :dict-data="dictData" @success="getLists" @close="showEdit = false" />

        <el-drawer
            v-model="infoDialogVisible"
            title="查看"
            size="920px"
            direction="rtl"
            :destroy-on-close="true"
            custom-class="weekly-report-drawer"
            @closed="handleInfoDialogClosed"
        >
            <info-popup
                v-if="infoDialogVisible"
                ref="infoRef"
                :dict-data="dictData"
                :row-id = "currentRowId"
                :row-data="currentRowData"
            />
            <template #footer>
                <el-button type="primary" class="drawer-close-btn" @click="infoDialogVisible = false">关闭</el-button>
            </template>
        </el-drawer>

    </div>
</template>

<script lang="ts" setup name="weeklyReportLists">
import { usePaging } from '@/hooks/usePaging'
import { useDictData } from '@/hooks/useDictOptions'
import { apiWeeklyReportLists, apiWeeklyReportDelete } from '@/api/weekly_report'
import { timeFormat } from '@/utils/util'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'
import InfoPopup from "@/views/weekly_report/info.vue";
import {ref, shallowRef} from "vue";


const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const infoRef = shallowRef<InstanceType<typeof InfoPopup>>()

// 是否显示编辑框
const showEdit = ref(false)

// 详情抽屉
const infoDialogVisible = ref(false)
const currentRowId = ref<any>(null)
const currentRowData = ref<Record<string, any> | null>(null)

// 查询条件
const queryParams = reactive({
    start_date: '',
    end_date: '',
    status: '',
    type: 1
})

// 选中数据
const selectData = ref<any[]>([])

// 表格选择后回调事件
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map(({ id }) => id)
}

// 获取字典数据
const { dictData } = useDictData('node_type')

// 分页相关
const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: apiWeeklyReportLists,
    params: queryParams
})

// 添加
const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}

// 编辑
const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    await editRef.value?.setFormData(data)
    editRef.value?.open('edit')
}

// 删除
const handleDelete = async (id: number | any[]) => {
    await feedback.confirm('确定要删除？')
    await apiWeeklyReportDelete({ id })
    await getLists()
}

// 抽屉相关
const handleInfoDialogClosed = () => {
    currentRowId.value = null
    currentRowData.value = null
}

const handleInfo = (row: any) => {
    currentRowId.value = row.id
    currentRowData.value = row
    infoDialogVisible.value = true
}

const formatHoursDisplay = (value: any) => {
    if (value === null || value === undefined || value === '') {
        return '-'
    }
    const hours = Number(value)
    if (!Number.isFinite(hours)) {
        return '-'
    }
    return Number.isInteger(hours) ? `${hours}` : `${Number(hours.toFixed(1))}`
}

getLists()
</script>

<style scoped>
.status-select {
    width: 180px;
}

:deep(.status-select .el-input__wrapper) {
    min-height: 32px;
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

.drawer-close-btn {
    min-width: 104px;
    height: 42px;
}
</style>

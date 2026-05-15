<template>
    <div>
        <el-card class="!border-none mb-4" shadow="never">
            <el-form
                class="mb-[-16px] weekly-report-query-form"
                :model="queryParams"
                inline
            >
                <el-form-item label="姓名" prop="name">
                    <el-input v-model="queryParams.name" placeholder="请输入姓名" clearable />
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
            <div class="mt-4">
                <el-table :data="pager.lists">
                    <el-table-column label="工号" prop="userInfo.number" />
                    <el-table-column label="姓名" prop="userInfo.name" />
                    <el-table-column label="岗位" prop="userInfo.jobs_name" />
                    <el-table-column label="周报时间" min-width="180">
                        <template #default="{ row }">
                            {{ row.start_date }} ~ {{ row.end_date }}
                        </template>
                    </el-table-column>
                    <el-table-column label="总工时" prop="total_hours" />
                    <el-table-column label="加班工时" prop="overtime_hours" />
                    <el-table-column label="审核状态" prop="status">
                        <template #default="{ row }">
                            <el-tag v-if="row.status == 0" type="warning">未审批</el-tag>
                            <el-tag v-else-if="row.status == 1" type="success">已审批</el-tag>
                            <el-tag v-else-if="row.status == 2" type="danger">驳回</el-tag>
                        </template>
                    </el-table-column>
                    <el-table-column label="提交时间" prop="submit_time" min-width="180" />

                    <el-table-column label="操作" width="180" fixed="right">
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
                </el-table>
            </div>
            <div class="flex mt-4 justify-end">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <el-drawer
            v-model="reviewDrawerVisible"
            :title="reviewTitle"
            size="920px"
            direction="rtl"
            :destroy-on-close="true"
            custom-class="weekly-report-drawer"
            @closed="handleReviewDrawerClosed"
        >
            <edit-popup
                v-if="reviewDrawerVisible"
                ref="editRef"
                @success="getLists"
                @close="reviewDrawerVisible = false"
            />
        </el-drawer>

    </div>
</template>

<script lang="ts" setup name="adminWeeklyReportLists">
import { usePaging } from '@/hooks/usePaging'
import { apiWeeklyReportLists } from '@/api/weekly_report'
import EditPopup from './edit.vue'
import {ref, shallowRef} from "vue";

const editRef = shallowRef<InstanceType<typeof EditPopup>>()

const reviewDrawerVisible = ref(false)
const reviewType = ref<'approve' | 'view'>('approve')
const reviewTitle = computed(() => (reviewType.value === 'view' ? '查看审批' : '审批'))

// 查询条件
const queryParams = reactive({
    name: '',
    status: '',
    type: 2
})

// 分页相关
const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: apiWeeklyReportLists,
    params: queryParams
})

// 审批/查看审批
const handleReview = async (data: any, type: 'approve' | 'view') => {
    reviewType.value = type
    reviewDrawerVisible.value = true
    await nextTick()
    editRef.value?.setFormData(data, reviewType.value)
}

const handleReviewDrawerClosed = () => {
    reviewType.value = 'approve'
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

@media (max-width: 768px) {
    :deep(.weekly-report-query-form .el-form-item) {
        width: 100%;
        margin-right: 0;
    }

    .status-select {
        width: 100%;
    }
}
</style>

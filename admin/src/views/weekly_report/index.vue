<template>
    <div>

        <el-card class="!border-none" v-loading="pager.loading" shadow="never">
            <el-button v-perms="['weekly_report.weekly_report/add']" type="primary" @click="handleAdd">
                <template #icon>
                    <icon name="el-icon-Plus" />
                </template>
                上传
            </el-button>
            <div class="mt-4">
                <el-table :data="pager.lists" @selection-change="handleSelectionChange">
                    <el-table-column label="周报文件名" prop="file_name"  show-overflow-tooltip/>

                    <el-table-column label="节点" prop="node_id" show-overflow-tooltip>
                        <template #default="{ row }">
                            <dict-value :options="dictData.node_type" :value="row.node_id" />
                        </template>
                    </el-table-column>
                    <el-table-column label="审核状态" prop="status">
                        <template #default="{ row }">
                            {{row.status == 1 ? '待审核' : '已审核'}}
                        </template>
                    </el-table-column>
                    <el-table-column label="上传时间" prop="create_time" min-width="180" />

                    <el-table-column label="操作" width="180" fixed="right">
                        <template #default="{ row }">
                             <el-button
                                 v-if="row.status == 1"
                                v-perms="['weekly_report.weekly_report/edit']"
                                type="primary"
                                link
                                @click="handleEdit(row)"
                            >
                                重新上传
                            </el-button>
                            <el-button
                                v-if="row.status == 2"
                                type="info"
                                link
                                @click="handleInfo(row)"
                            >
                                查看审批
                            </el-button>
                            <el-button
                                v-if="row.status == 1"
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

        <!-- 详情抽屉 -->
        <el-drawer
            v-model="infoDrawerVisible"
            title="审批详情"
            :size="'45%'"
            :destroy-on-close="true"
            @closed="handleInfoDrawerClosed"
        >
            <info-popup
                v-if="infoDrawerVisible"
                ref="infoRef"
                :dict-data="dictData"
                :row-id = "currentRowId"
            />
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
import {ref, shallowRef} from "vue";


const editRef = shallowRef<InstanceType<typeof EditPopup>>()

// 是否显示编辑框
const showEdit = ref(false)


// 查询条件
const queryParams = reactive({
    file_name: '',
    node_id: '',
    node: '',
    type:1,
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
    editRef.value?.open('edit')
    editRef.value?.setFormData(data)
}

// 删除
const handleDelete = async (id: number | any[]) => {
    await feedback.confirm('确定要删除？')
    await apiWeeklyReportDelete({ id })
    getLists()
}



//抽屉相关
import InfoPopup from "@/views/weekly_report/info.vue";
const infoRef = shallowRef<InstanceType<typeof InfoPopup>>()
const infoDrawerVisible = ref(false)
const currentRowId = ref<any>(null)

const handleInfoDrawerClosed = () => {
    currentRowId.value = null
}

const handleInfo = (row: any) => {
    currentRowId.value = row.id
    infoDrawerVisible.value = true
}

getLists()
</script>


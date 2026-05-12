<template>
    <div>
        <el-card class="!border-none mb-4" shadow="never">
            <el-form
                class="mb-[-16px]"
                :model="queryParams"
                inline
            >

                <el-form-item label="节点" class="w-[280px]" prop="node_id">
                    <el-select class="w-[280px]" v-model="queryParams.node_id" clearable placeholder="请选择节点">
                        <el-option label="全部" value=""></el-option>
                        <el-option
                            v-for="(item, index) in dictData.node_type"
                            :key="index"
                            :label="item.name"
                            :value="item.value"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item class="w-[280px]" label="审核状态">
                    <el-select v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in dictData.status_type"
                            :key="index"
                            :label="item.name"
                            :value="item.value"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item label="姓名" prop="node">
                    <el-input class="w-[280px]" v-model="queryParams.name" clearable placeholder="请输入姓名" />
                </el-form-item>




<!--                <el-form-item label="节点" prop="node">-->
<!--                    <el-input class="w-[280px]" v-model="queryParams.node" clearable placeholder="请输入节点" />-->
<!--                </el-form-item>-->
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none" v-loading="pager.loading" shadow="never">
<!--            <el-button v-perms="['weekly_report.weekly_report/add']" type="primary" @click="handleAdd">-->
<!--                <template #icon>-->
<!--                    <icon name="el-icon-Plus" />-->
<!--                </template>-->
<!--                新增-->
<!--            </el-button>-->
<!--            <el-button-->
<!--                v-perms="['weekly_report.weekly_report/delete']"-->
<!--                :disabled="!selectData.length"-->
<!--                @click="handleDelete(selectData)"-->
<!--            >-->
<!--                删除-->
<!--            </el-button>-->
            <div class="mt-4">
                <el-table :data="pager.lists" @selection-change="handleSelectionChange">
                    <el-table-column type="selection" width="55" />
                    <el-table-column label="工号" prop="userInfo.number"  />
                    <el-table-column label="姓名" prop="userInfo.name"  />
                    <el-table-column label="岗位" prop="userInfo.jobs_name"  />

                    <el-table-column label="周报文件名" prop="file_name" show-overflow-tooltip/>

                    <el-table-column label="节点" prop="node" show-overflow-tooltip/>


                    <el-table-column label="审核状态" prop="status">
                        <template #default="{ row }">
                            {{row.status == 1 ? '待审核' : '已审核'}}
                        </template>
                    </el-table-column>

                    <el-table-column label="操作" width="180" fixed="right">
                        <template #default="{ row }">
                            <el-button
                                v-if="row.status == 1"
                                v-perms="['weekly_report.weekly_report/edit']"
                                type="primary"
                                link
                                @click="handleEdit(row)"
                            >
                                审核
                            </el-button>

                            <el-button
                                type="primary"
                                link
                                @click="handleDownload(row.file_url, row.file_name)"
                            >
                                下载
                            </el-button>

                            <el-button
                                v-if="row.status == 2"
                                type="info"
                                link
                                @click="handleInfo(row)"
                            >
                                查看审核
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
            title="审核详情"
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
    node: ''
})

// 选中数据
const selectData = ref<any[]>([])

// 表格选择后回调事件
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map(({ id }) => id)
}

// 获取字典数据
const { dictData } = useDictData('node_type,status_type')

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
import InfoPopup from "@/views/admin_weekly_report/info.vue";
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

const handleDownload = (file_url: any, file_name: any) => {
    const link = document.createElement('a');
    link.href = file_url;
    link.download = file_name;
    link.click();
}


getLists()
</script>


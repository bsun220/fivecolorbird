<template>
    <div>

        <el-card class="!border-none" v-loading="pager.loading" shadow="never">

            <div class="mt-4">
                <el-table :data="pager.lists">
                    <el-table-column label="ID" prop="id" show-overflow-tooltip />
                    <el-table-column label="内容" prop="content" show-overflow-tooltip />
                    <el-table-column label="创建时间" prop="create_time" show-overflow-tooltip />
                    <el-table-column label="操作">
                        <template #default="{ row }">
                            <el-button type="primary" link @click="handleView(row)">查看</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
            <div class="flex mt-4 justify-end">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
        <edit-popup v-if="showEdit" ref="editRef" :dict-data="dictData" @success="getLists" @close="showEdit = false" />
    </div>
</template>

<script lang="ts" setup name="systemMsgLists">
import { usePaging } from '@/hooks/usePaging'
import { useDictData } from '@/hooks/useDictOptions'
import { apiSystemMsgLists, apiSystemMsgDelete } from '@/api/system_msg'
import { timeFormat } from '@/utils/util'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const editRef = shallowRef<InstanceType<typeof EditPopup>>()
// 是否显示编辑框
const showEdit = ref(false)

// 查询条件
const queryParams = reactive({
    
})

// 选中数据
const selectData = ref<any[]>([])

// 表格选择后回调事件
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map(({ id }) => id)
}

// 获取字典数据
const { dictData } = useDictData('')

// 分页相关
const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: apiSystemMsgLists,
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
    await apiSystemMsgDelete({ id })
    getLists()
}

// 查看跳转
const handleView = (row: any) => {
    // 假设 type=1 跳转A页面，type=2跳转B页面，可根据实际调整
    if (row.type === 1) {
        router.push(`/work/user/performance`)
    } else if (row.type === 2) {
        router.push(`/work/weekly_report`)
    } else if (row.type === 3) {
        router.push(`/work/workbench/user_index`)
    }
}

getLists()
</script>


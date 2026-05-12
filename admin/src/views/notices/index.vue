<template>
    <div>
        <el-card class="!border-none mb-4" shadow="never">
            <el-form
                class="mb-[-16px]"
                :model="queryParams"
                inline
            >
                <el-form-item label="部门" prop="pid">
                    <el-select class="w-[280px]" style="width:280px" v-model="queryParams.pid" clearable placeholder="请选择部门">
                        <el-option
                            v-for="(item, index) in deptAllList"
                            :key="index" 
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
<!--                <el-form-item label="" prop="content">-->
<!--                    <el-input class="w-[280px]" v-model="queryParams.content" clearable placeholder="请输入" />-->
<!--                </el-form-item>-->

                <el-form-item label="发布时间" prop="create_time">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                        type="daterange"
                        range-separator="至"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                    />
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none" v-loading="pager.loading" shadow="never">
            <el-button v-perms="['notices.notices/add']" type="primary" @click="handleAdd">
                <template #icon>
                    <icon name="el-icon-Plus" />
                </template>
                新增
            </el-button>
            <el-button
                v-perms="['notices.notices/delete']"
                :disabled="!selectData.length"
                @click="handleDelete(selectData)"
            >
                删除
            </el-button>
            <div class="mt-4">
                <el-table :data="pager.lists" @selection-change="handleSelectionChange">
                    <el-table-column type="selection" width="55" />
                    <el-table-column label="部门" prop="dept_name">
<!--                        <template #default="{ row }">-->
<!--                            <dict-value :options="deptAllList" :value="row.pid" />-->
<!--                        </template>-->
                    </el-table-column>
                    <el-table-column label="公告" prop="content" show-overflow-tooltip>
                        <template #default="{row}">
                            <div class="flex items-center">
                                <!-- 优化Popover触发方式 -->
                                <el-popover
                                    placement="bottom-start"
                                    :width="Math.min(400, row.content.length * 10 + 50)"
                                    trigger="click"
                                    popper-class="reward-note-popover"
                                >
                                    <template #reference>
                                        <el-link
                                            type="primary"
                                            :underline="false"
                                            class="view-link"
                                            v-show="row.content.length > 0"
                                        >
                                            查看公告
                                        </el-link>
                                    </template>
                                    <div class="p-2 max-h-[300px] overflow-auto">
                                        <pre class="whitespace-pre-wrap font-sans">{{ row.content }}</pre>
                                    </div>
                                </el-popover>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="发布时间" prop="create_time" min-width="180" />
                    <el-table-column label="操作" width="120" fixed="right">
                        <template #default="{ row }">
                             <el-button
                                v-perms="['notices.notices/edit']"
                                type="primary"
                                link
                                @click="handleEdit(row)"
                            >
                                编辑
                            </el-button>
                            <el-button
                                v-perms="['notices.notices/delete']"
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
    </div>
</template>

<script lang="ts" setup name="noticesLists">
import { usePaging } from '@/hooks/usePaging'
import { useDictData } from '@/hooks/useDictOptions'
import { apiNoticesLists, apiNoticesDelete, apiDeptAllList } from '@/api/notices'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'
import {onMounted} from "vue";

const editRef = shallowRef<InstanceType<typeof EditPopup>>()
// 是否显示编辑框
const showEdit = ref(false)


// 查询条件
const queryParams = reactive({
    pid: '',
    content: '',
    dept_name : '',
})

// 选中数据
const selectData = ref<any[]>([])

// 表格选择后回调事件
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map(({ id }) => id)
}

// 获取字典数据
const { dictData } = useDictData('work_score_type')

const deptAllList = ref<any[]>([]);


// 分页相关
const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: apiNoticesLists,
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
    await apiNoticesDelete({ id })
    getLists()
}



const loading = async () => {
    const data = await apiDeptAllList();
    deptAllList.value = data;
    console.log(deptAllList)
}

onMounted(() => {
    loading()
})

getLists()
</script>


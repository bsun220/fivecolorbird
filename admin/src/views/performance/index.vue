<template>
    <div>
        <el-card class="!border-none mb-4" shadow="never">
            <el-form
                class="mb-[-16px]"
                :model="queryParams"
                inline
            >
                <el-form-item label="用户" prop="user_id"  class="w-[280px]">
                    <el-select class="w-[280px]" v-model="queryParams.user_id" filterable clearable placeholder="请选择用户">
                        <el-option label="全部" value=""></el-option>
                        <el-option 
                            v-for="(item, index) in userList"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="统计月份" prop="statistical_month">
                    <el-date-picker
                        v-model="queryParams.statistical_month"
                        type="month"
                        format="YYYY年MM月"
                        value-format="YYYY-MM"
                        placeholder="选择统计月份">
                    </el-date-picker>
                </el-form-item>
                

<!--                <el-form-item label="预计发放日期" prop="issue_date">
                    <daterange-picker
                        v-model:startTime="queryParams.start_time"
                        v-model:endTime="queryParams.end_time"
                        type="daterange"
                        range-separator="至"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                    />
                </el-form-item>
                

                <el-form-item label="工作评分" prop="work_score" class="w-[280px]">
                    <el-select class="w-[280px]" v-model="queryParams.work_score" clearable placeholder="请选择工作评分">
                        <el-option label="全部" value=""></el-option>
                        <el-option 
                            v-for="(item, index) in dictData.work_score_type"
                            :key="index" 
                            :label="item.name"
                            :value="item.value"
                        />
                    </el-select>
                </el-form-item>-->

                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none" v-loading="pager.loading" shadow="never">
            <el-button v-perms="['performance.performance/add']" type="primary" @click="handleAdd">
                <template #icon>
                    <icon name="el-icon-Plus" />
                </template>
                新增绩效
            </el-button>
            <el-button
                v-perms="['performance.performance/delete']"
                :disabled="!selectData.length"
                @click="handleDelete(selectData)"
                type="danger"
            >
                <template #icon>
                    <icon name="el-icon-Delete" />
                </template>
                删除
            </el-button>
            <div class="mt-4">
                <el-table :data="pager.lists" @selection-change="handleSelectionChange">
                    <el-table-column type="selection" width="55" />
                    <el-table-column label="工号" prop="userInfo.number" width="100" show-overflow-tooltip />
                    <el-table-column label="用户" prop="userInfo.name" width="100" show-overflow-tooltip />
                    <el-table-column label="岗位" prop="userInfo.jobs_name" width="110" show-overflow-tooltip />
                    <el-table-column label="部门" prop="userInfo.dept_name" width="110" show-overflow-tooltip />
                    <el-table-column label="统计月份" prop="statistical_month" width="110" :formatter="(_,__,v) => formatMonthText(v)" show-overflow-tooltip />
                    <el-table-column label="工作评分" prop="work_score" width="80">
                        <template #default="{ row }">
                            <dict-value :options="dictData.work_score_type" :value="row.work_score" />
                        </template>
                    </el-table-column>

                    <el-table-column label="绩效工资" prop="merit_pay" width="100" show-overflow-tooltip />
                    <el-table-column label="发放日期" prop="issue_date" width="110" show-overflow-tooltip />
                    <el-table-column label="累计绩效" prop="cumulative_merit_pay" width="100" show-overflow-tooltip />
                    <el-table-column label="剩余加班工时" prop="remaining_overtime_hours" width="130" show-overflow-tooltip />

                    <!--
                    <el-table-column label="已发绩效奖金" prop="issued" show-overflow-tooltip />
                    <el-table-column label="奖励金额" prop="reward_amount" show-overflow-tooltip />
                    <el-table-column label="奖励金额说明" prop="reward_amount_note" show-overflow-tooltip />
                    <el-table-column label="处罚金额" prop="penalty_amount" show-overflow-tooltip />
                    <el-table-column label="处罚金额说明" prop="penalty_amount_note" show-overflow-tooltip />
                    <el-table-column label="工作点评" prop="work_comment" show-overflow-tooltip />
                    -->
                    <el-table-column label="操作" width="150" fixed="right">
                        <template #default="{ row }">
                             <el-button
                                v-perms="['performance.performance/edit']"
                                type="primary"
                                link
                                @click="handleEdit(row)"
                            >
                                编辑
                            </el-button>
                            <el-button
                                type="info"
                                link
                                @click="handleInfo(row)"
                            >
                                详情
                            </el-button>
                            <el-button
                                v-perms="['performance.performance/delete']"
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
        <edit-popup v-if="showEdit" ref="editRef" :dict-data="dictData" :user-list="userList" @success="getLists" @close="showEdit = false" />

        <!-- 详情抽屉 -->
        <el-drawer
            v-model="infoDrawerVisible"
            title="绩效详情"
            size="860px"
            :destroy-on-close="true"
            custom-class="performance-info-drawer"
            @closed="handleInfoDrawerClosed"
        >
            <info-popup
                v-if="infoDrawerVisible"
                ref="infoRef"
                :dict-data="dictData"
                :row-id = "currentRowId"
                @close="infoDrawerVisible = false"
                @edit="handleInfoEdit"
            />
        </el-drawer>
    </div>
</template>

<script lang="ts" setup name="performanceLists">
import {onMounted} from "vue";
import { usePaging } from '@/hooks/usePaging'
import { useDictData } from '@/hooks/useDictOptions'
import { apiPerformanceLists, apiPerformanceDelete } from '@/api/performance'
import { timeFormat, formatMonthText } from '@/utils/util'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'
import {adminAll} from "@/api/perms/admin";


const editRef = shallowRef<InstanceType<typeof EditPopup>>()
// 是否显示编辑框
const showEdit = ref(false)


// 查询条件
const queryParams = reactive({
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

// 选中数据
const selectData = ref<any[]>([])

// 用户数据
const userList = reactive([]);


// 表格选择后回调事件
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map(({ id }) => id)
}

// 获取字典数据
const { dictData } = useDictData('work_score_type')

// 分页相关
const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: apiPerformanceLists,
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
    await apiPerformanceDelete({ id })
    getLists()
}

const loading = async () => {
    const data = await adminAll([]);

    // 正确遍历对象并赋值到formData
    Object.keys(data).forEach(key => {
        userList[key] = data[key];
    });
}

onMounted(() => {
    loading()
})


//抽屉相关
import InfoPopup from "@/views/performance/info.vue";
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

const handleInfoEdit = async (data: any) => {
    infoDrawerVisible.value = false
    await handleEdit(data)
}



getLists()
</script>

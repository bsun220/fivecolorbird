<template>
    <div>
        <el-card class="!border-none mb-4" shadow="never">
            <el-form
                class="mb-[-16px]"
                :model="queryParams"
                inline
            >
                <el-form-item label="预约日期" prop="date">
                    <daterange-picker
                        v-model="queryParams.date"
                        type="date"
                        placeholder="预约日期"
                        format="YYYY-MM-DD"
                        value-format="YYYY-MM-DD"
                    />
                </el-form-item>

<!--                <el-form-item label="预约日期" prop="name">-->
<!--                    <el-input class="w-[280px]" v-model="queryParams.date" clearable placeholder="请输入预约日期" />-->
<!--                </el-form-item>-->
                
                <el-form-item label="预约人" prop="name">
                    <el-input class="w-[280px]" v-model="queryParams.name" clearable placeholder="请输入预约人" />
                </el-form-item>

                <el-form-item label="预约活动" prop="name">
                    <el-input class="w-[280px]" v-model="queryParams.activity.name" clearable placeholder="请输入预约活动" />
                </el-form-item>

                <el-form-item label="手机号" prop="mobile">
                    <el-input class="w-[280px]" v-model="queryParams.mobile" clearable placeholder="请输入手机号" />
                </el-form-item>

                <el-form-item class="w-[280px]" label="状态">
                    <el-select  v-model="queryParams.status" label="请选择状态">
                        <el-option label="全部" value=""></el-option>
                        <el-option
                            v-for="(item, index) in StatusMap"
                            :key="index"
                            :label="item"
                            :value="index"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none" v-loading="pager.loading" shadow="never">
<!--            <el-button v-perms="['activity.reservation/add']" type="primary" @click="handleAdd">
                <template #icon>
                    <icon name="el-icon-Plus" />
                </template>
                新增
            </el-button>-->
            <el-button
                v-perms="['activity.reservation/delete']"
                :disabled="!selectData.length"
                @click="handleDelete(selectData)"
            >
                删除
            </el-button>
            <div class="mt-4">
                <el-table :data="pager.lists" @selection-change="handleSelectionChange">
                    <el-table-column type="selection" width="55" />
                    <el-table-column label="用户" prop="user.account" show-overflow-tooltip />
                    <el-table-column label="预约活动" prop="activity.name" show-overflow-tooltip />
                    <el-table-column label="预约日期" prop="date" show-overflow-tooltip />
                    <el-table-column label="预约时间段" prop="time_slot" show-overflow-tooltip />
                    <el-table-column label="预约人" prop="name" show-overflow-tooltip />
                    <el-table-column label="手机号" prop="mobile" show-overflow-tooltip />
                    <el-table-column label="人数" prop="num" show-overflow-tooltip />
                    <el-table-column label="备注" prop="note" show-overflow-tooltip />
                    <el-table-column label="公司名称" prop="company" show-overflow-tooltip />
                    <el-table-column label="核销状态" prop="status_desc" show-overflow-tooltip />
                    <el-table-column label="操作" width="150" fixed="right">
                        <template #default="{ row }">
                            <el-button v-if="row.status == 1"
                                v-perms="['activity.reservation/verification']"
                                type="primary"
                                link
                                @click="handleVerification(row.id)"
                            >
                                核销
                            </el-button>
                             <el-button
                                v-perms="['activity.reservation/edit']"
                                type="primary"
                                link
                                @click="handleEdit(row)"
                            >
                                编辑
                            </el-button>
                            <el-button
                                v-perms="['activity.reservation/delete']"
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

<script lang="ts" setup name="reservationLists">
import { usePaging } from '@/hooks/usePaging'
import { useDictData } from '@/hooks/useDictOptions'
import {apiReservationLists, apiReservationDelete, apiReservationVerification} from '@/api/reservation'
import { timeFormat } from '@/utils/util'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'
import {ClientEnum} from "@/enums/appEnums";

const size = ref<'default' | 'large' | 'small'>('default')

const editRef = shallowRef<InstanceType<typeof EditPopup>>()
// 是否显示编辑框
const showEdit = ref(false)


// 查询条件
const queryParams = reactive({
    date: '',
    name: '',
    mobile: '',
    activity:{
    }
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
    fetchFun: apiReservationLists,
    params: queryParams
})

// 添加
const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}


// 核销
const handleVerification = async (id: number | any[]) => {
    await feedback.confirm('确定要核销？')
    await apiReservationVerification({ id })
    getLists()
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
    await apiReservationDelete({ id })
    getLists()
}
const StatusMap = {
    0 : '取消',
    1 : '待核销',
    2 : '已核销'
}

getLists()
</script>


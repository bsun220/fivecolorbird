<template>
    <div class="admin">
        <el-card class="!border-none" shadow="never">
            <el-form class="mb-[-16px]" :model="formData" inline>
                <el-form-item class="w-[280px]" label="人员账号">
                    <el-input
                        v-model="formData.account"
                        placeholder="请输入人员账号"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="人员姓名">
                    <el-input
                        v-model="formData.name"
                        placeholder="请输入人员名称"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
<!--                <el-form-item class="w-[280px]" label="在职状态">
                    <el-select v-model="formData.disable">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in dictData.disable_type"
                            :key="index"
                            :label="item.name"
                            :value="item.value"
                        />
                    </el-select>
                </el-form-item>-->
                <el-form-item class="w-[280px]" label="人员角色">
                    <el-select v-model="formData.role_id">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in optionsData.role"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>


<!--                <el-form-item class="w-[280px]" label="所属部门">
                    <el-select v-model="formData.dept_id">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in optionsData.dept"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item class="w-[280px]" label="所属岗位">
                    <el-select v-model="formData.jobs_id">
                        <el-option label="全部" value="" />
                        <el-option
                            v-for="(item, index) in optionsData.jobs"
                            :key="index"
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>-->

<!--                <el-form-item label="入职日期" prop="date">
                    <daterange-picker
                        v-model="formData.entry_time"
                        type="date"
                        placeholder="入职日期"
                        format="YYYY-MM-DD"
                        value-format="YYYY-MM-DD"
                    />
                </el-form-item>-->

                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="adminLists"
                        :params="formData"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card v-loading="pager.loading" class="mt-4 !border-none" shadow="never">
            <el-button v-perms="['auth.admin/add']" type="primary" @click="handleAdd">
                <template #icon>
                    <icon name="el-icon-Plus" />
                </template>
                新增
            </el-button>
            <!--<el-button
                v-perms="['auth.admin/disable']"
                :disabled="!selectData.length"
                @click="handleDisable(selectData)"
                type="danger"
            >
                <template #icon>
                    <icon name="el-icon-Delete" />
                </template>
                离职
            </el-button>-->

            <div class="mt-4">
                <el-table :data="pager.lists"
                          size="large"
                          @selection-change="handleSelectionChange"
                >
                    <el-table-column type="selection" width="55" />

                    <el-table-column label="工号" prop="number" min-width="80" />
                    <el-table-column label="姓名" prop="name" min-width="100" />
                    <el-table-column label="登录账号" prop="account" min-width="100" />

                    <el-table-column
                        label="角色"
                        prop="role_name"
                        min-width="100"
                        show-tooltip-when-overflow
                    />
                    <el-table-column label="最近登录时间" prop="login_time" min-width="180" />
                    <el-table-column label="最近登录IP" prop="login_ip" min-width="120" />
                    <el-table-column label="操作" width="180" fixed="right">
                        <template #default="{ row }">
                            <el-button
                                v-perms="['auth.admin/edit']"
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
                                v-if="row.root != 1"
                                v-perms="['auth.admin/delete']"
                                type="danger"
                                link
                                @click="handleDisable(row.id)"
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

        <!-- 编辑弹窗 -->
        <edit-popup v-if="showEdit" ref="editRef" :dict-data="dictData" @success="getLists" @close="showEdit = false" />


        <!-- 详情抽屉 -->
        <el-drawer
                v-model="infoDrawerVisible"
                title="人员详情"
                :size="'45%'"
                :destroy-on-close="true"
                @closed="handleInfoDrawerClosed"
        >
            <info-popup
                v-if="infoDrawerVisible"
                ref="infoRef"
                :row-id = "currentRowId"
            />
        </el-drawer>
    </div>
</template>

<script lang="ts" setup name="admin">
import { ref, shallowRef, reactive, onMounted, nextTick } from 'vue'
import { adminEdit, adminLists, adminDelete } from '@/api/perms/admin'
import { roleAll } from '@/api/perms/role'
import { useDictOptions } from '@/hooks/useDictOptions'
import { usePaging } from '@/hooks/usePaging'
import { useDictData } from '@/hooks/useDictOptions'
import feedback from '@/utils/feedback'
import EditPopup from './edit.vue'
import {jobsAll} from "@/api/org/post";
import {deptAll} from "@/api/org/department";

const editRef = shallowRef<InstanceType<typeof EditPopup>>()
const showEdit = ref(false)

const { dictData } = useDictData('sex,qualification_type,card_type,disable_type')

const formData = reactive({
    account: '',
    name: '',
    role_id: '',
    disable: '',
    entry_time: '',
})

const { pager, getLists, resetParams, resetPage } = usePaging({
    fetchFun: adminLists,
    params: formData
})

const selectData = ref<any[]>([])
const handleSelectionChange = (val: any[]) => {
    selectData.value = val.map(({ id }) => id)
}

const changeStatus = (data: any) => {
    adminEdit({
        id: data.id,
        avatar: data.avatar,
        account: data.account,
        name: data.name,
        role_id: data.role_id,
        jobs_id: data.jobs_id,
        dept_id: data.dept_id,
        disable: data.disable,
        multipoint_login: data.multipoint_login,

        age: data.age,
        email: data.email,
        card_val: data.card_val,
        salary: data.salary,
        address: data.address,
        entry_time: data.entry_time,
        sex: data.sex,
        card: data.card,
        qualification: data.qualification,
        mobile: data.mobile,
        number: data.number,
        contract_file: data.contract_file,

    }).finally(() => {
        getLists()
    })
}

const handleAdd = async () => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('add')
}

const handleEdit = async (data: any) => {
    showEdit.value = true
    await nextTick()
    editRef.value?.open('edit')
    editRef.value?.setFormData(data)
}

const handleDisable = async (id: number) => {
    await feedback.confirm('确定要删除？')
    await adminDelete({ id })
    getLists()
}

const { optionsData } = useDictOptions<{
    role: any[],
    jobs: any[],
    dept: any[],
}>({
    role: {
        api: roleAll
    },
    jobs: {
        api: jobsAll
    },
    dept: {
        api: deptAll
    },
})

onMounted(() => {
    getLists()
})


// 抽屉相关
import InfoPopup from "@/views/admin/info.vue";
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


</script>

<style scoped>
:deep(.el-drawer__body) {
    padding: 20px;
    overflow-y: auto;
}
</style>

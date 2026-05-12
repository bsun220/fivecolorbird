<style>
.title {border-left: 4px solid #045dff; padding-left: 8px;}
</style>
<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="1000px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="84px" :rules="formRules">
                <div class="font-medium mb-7 title">基础信息</div>
                <!--基础信息-->
                <!--第一行-->
                <div class="flex gap-4 mb-4">
                    <!-- 名称输入框 -->
                    <el-form-item label="名称" prop="name">
                        <el-input v-model="formData.name" placeholder="请输入名称" clearable style="width: 180px"/>
                    </el-form-item>
                    <el-form-item label="归属部门" prop="dept_id">
                        <el-tree-select
                            class="flex-1"
                            v-model="formData.dept_id"
                            :data="optionsData.dept"
                            clearable
                            multiple
                            :multiple-limit="1"
                            node-key="id"
                            style="width: 180px"
                            :props="{
                            value: 'id',
                            label: 'name',
                            disabled(data: any) {
                                return data.status !== 1
                            }
                        }"
                            check-strictly
                            :default-expand-all="true"
                            placeholder="请选择上级部门"
                        />
                    </el-form-item>
                    <el-form-item label="岗位" prop="jobs_id">
                        <el-select
                            class="flex-1"
                            v-model="formData.jobs_id"
                            clearable
                            placeholder="请选择岗位"
                            multiple
                            :multiple-limit="1"
                            style="width: 180px"
                        >
                            <el-option
                                v-for="(item, index) in optionsData.jobs"
                                :key="index"
                                :label="item.name"
                                :value="item.id"

                            />
                        </el-select>
                    </el-form-item>
                </div>
                <!--第二行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="性别" prop="sex">
                        <el-select class="flex-1"
                                   v-model="formData.sex"
                                   clearable
                                   placeholder="请选择性别"
                                   style="width: 180px"
                        >
                            <el-option
                                v-for="(item, index) in dictData.sex"
                                :key="index"
                                :label="item.name"
                                :value="Number(item.value)"
                            />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="年龄" prop="age">
                        <el-input
                            v-model="formData.age"
                            placeholder="年龄"
                            clearable
                            style="width: 180px"
                        />
                    </el-form-item>
                    <el-form-item label="学历" prop="qualification">
                        <el-select class="flex-1" v-model="formData.qualification" clearable placeholder="请选择学历" style="width: 180px" >
                            <el-option
                                v-for="(item, index) in dictData.qualification_type"
                                :key="index"
                                :label="item.name"
                                :value="Number(item.value)"
                            />
                        </el-select>
                    </el-form-item>
                </div>
                <!--第三行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="手机号" prop="mobile">
                        <el-input
                            v-model="formData.mobile"
                            type="tel"
                            placeholder="手机号"
                            clearable
                            style="width: 180px"
                        />
                    </el-form-item>
                    <el-form-item label="邮箱地址" prop="email">
                        <el-input
                            v-model="formData.email"
                            type="email"
                            placeholder="邮箱地址"
                            clearable
                            style="width: 180px"
                        />
                    </el-form-item>
                    <el-form-item label="联系地址" prop="address">
                        <el-input
                            v-model="formData.address"
                            placeholder="联系地址"
                            clearable
                            style="width: 180px"
                        />
                    </el-form-item>
                </div>
                <!--第四行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="证件类型" prop="card">
                        <el-select class="flex-1" v-model="formData.card" clearable placeholder="请选择证件类型" style="width: 180px" >
                            <el-option
                                v-for="(item, index) in dictData.card_type"
                                :key="index"
                                :label="item.name"
                                :value="Number(item.value)"
                            />
                        </el-select>

                    </el-form-item>
                    <el-form-item label="证件号" prop="card_val">
                        <el-input
                            v-model="formData.card_val"
                            placeholder="证件号"
                            clearable
                            style="width: 180px"
                        />
                    </el-form-item>
                    <el-form-item label="月薪资" prop="salary">
                        <el-input
                            v-model="formData.salary"
                            placeholder="月薪资"
                            clearable
                            style="width: 180px"
                        />
                    </el-form-item>
                </div>
                <!--第五行-->
                <div class="flex gap-4 mb-4">
                    <el-form-item label="入职时间" prop="entry_time">
                        <el-date-picker
                            class="flex-1 !flex"
                            v-model="formData.entry_time"
                            clearable
                            type="date"
                            value-format="YYYY-MM-DD"
                            placeholder="选择入职时间"
                            style="width: 180px"
                        >
                        </el-date-picker>
                    </el-form-item>
                    <el-form-item label="在职状态" prop="disable">
                        <el-select class="flex-1"
                                   v-model="formData.disable"
                                   clearable placeholder="请选择在职状态"
                                   style="width: 180px" >
                            <el-option
                                v-for="(item, index) in dictData.disable_type"
                                :key="index"
                                :label="item.name"
                                :value="Number(item.value)"
                            />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="工号" prop="number">
                        <el-input
                            v-model="formData.number"
                            disabled
                            placeholder="工号"
                            style="width: 180px"
                        />
                    </el-form-item>
                </div>

                <div class="font-medium mb-7 title">账号信息</div>
                <!--账号信息-->
                <!--第一行-->
                <div class="flex gap-4 mb-4">
                    <!-- 账号输入框 -->
                    <el-form-item label="账号" prop="account">
                        <el-input
                            v-model="formData.account"
                            :disabled="formData.root == 1"
                            placeholder="请输入账号"
                            clearable
                            style="width: 180px"
                        />
                    </el-form-item>

                    <!-- 密码输入框 -->
                    <el-form-item label="密码" prop="password">
                        <el-input
                            v-model="formData.password"
                            show-password
                            clearable
                            placeholder="请输入密码"
                            style="width: 180px"
                        />
                    </el-form-item>

                    <!-- 确认密码输入框 -->
                    <el-form-item label="确认密码" prop="password_confirm">
                        <el-input
                            v-model="formData.password_confirm"
                            show-password
                            clearable
                            placeholder="请输入确认密码"
                            style="width: 180px"
                        />
                    </el-form-item>
                </div>
                <!--第二行-->
                <div class="flex gap-4 mb-4">
                    <!-- 角色选择框 -->
                    <el-form-item label="角色" prop="role_id" v-if="formData.root != 1">
                        <el-select
                            v-model="formData.role_id"
                            :disabled="formData.root == 1"
                            class="flex-1"
                            placeholder="请选择角色"
                            multiple
                            :multiple-limit="1"
                            clearable
                            style="width: 180px"
                        >
                            <el-option
                                v-for="(item, index) in optionsData.role"
                                :key="index"
                                :label="item.name"
                                :value="item.id"
                            />
                        </el-select>
                    </el-form-item>
                </div>

                <div class="font-medium mb-7 title">附件</div>
                <div class="flex gap-4 mb-4" >
                    <el-form-item label="头像">
                        <material-picker v-model="formData.avatar" :limit="1" />
                    </el-form-item>
                    <el-form-item label="上传附件">
                        <material-picker
                            v-model="formData.contract_file"
                            type="file"
                            :limit="5"
                        />
                    </el-form-item>
                </div>

                <div class="flex gap-4 mb-4" >
                    <el-form-item label="上传附件">
                        <upload
                            type="file"
                            v-model="formData.contract_file"
                            :limit="5"
                            @change="onChange"
                            @success="onSuccess"
                            :show-progress="true"
                        >
                            <el-button type="primary">上传文件</el-button>
                        </upload>
                    </el-form-item>
                </div>
                <div class="flex gap-4 mb-4" >
                    <el-table
                        :data="tableData"
                        style="width: 100%"
                    >
                        <el-table-column
                            prop="name"
                            label="文件名"
                        >
                        </el-table-column>
                        <el-table-column>

                        </el-table-column>
                    </el-table>
                </div>



                <!-- 多处登录 -->
                <el-form-item label="多处登录">
                    <div>
                        <el-switch
                            v-model="formData.multipoint_login"
                            :active-value="1"
                            :inactive-value="0"
                        />
                        <div class="form-tips">允许多人同时在线登录</div>
                    </div>
                </el-form-item>


            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup>
import type { FormInstance } from 'element-plus'

import {deptAll} from '@/api/org/department'
import { jobsAll } from '@/api/org/post'
import { adminAdd, adminDetail, adminEdit, adminCount } from '@/api/perms/admin'
import { roleAll } from '@/api/perms/role'
import Popup from '@/components/popup/index.vue'
import { useDictOptions } from '@/hooks/useDictOptions'
import type { PropType } from 'vue'
import { nextTick } from 'vue'
defineProps({
    dictData: {
        type: Object as PropType<Record<string, any[]>>,
        default: () => ({})
    }
})

const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑人员' : '新增人员'
})

const formData = reactive({
    id: '',
    account: '',
    name: '',
    age: '',
    email: '',

    card_val: '',
    salary: '',
    address: '',
    entry_time: '',
    mobile: '',
    number: '',
    sex: '',
    card: '',
    qualification: '',
    dept_id: [],
    jobs_id: [],
    role_id: [],
    contract_file: [] as Array<{ name: string; url: string }>,  // 明确类型
    avatar: '',
    password: '',
    password_confirm: '',
    disable:'',
    multipoint_login: 1,
    root: 0
})

const passwordConfirmValidator = (rule: object, value: string, callback: any) => {
    if (formData.password) {
        if (!value) callback(new Error('请再次输入密码'))
        if (value !== formData.password) callback(new Error('两次输入密码不一致!'))
    }
    callback()
}

const roleIdValidator = (rule: object, value: string, callback: any) => {
    if (formData.root) {
        callback()
    } else {
        if (formData.role_id.length) {
            callback()
        } else {
            callback(new Error('请选择角色'))
        }
    }
}
const formRules = reactive({
    account: [
        {
            required: true,
            message: '请输入账号',
            trigger: ['blur']
        }
    ],
    name: [
        {
            required: true,
            message: '请输入名称',
            trigger: ['blur']
        }
    ],
    mobile: [
        {
            required: true,
            message: '请输入手机号',
            trigger: ['blur']
        }
    ],
    salary: [
        {
            required: true,
            message: '请输入薪资',
            trigger: ['blur']
        }
    ],
    card: [
        {
            required: true,
            message: '请输入薪资',
            trigger: ['blur']
        }
    ],
    card_val: [
        {
            required: true,
            message: '请输入证件号',
            trigger: ['blur']
        }
    ],
    entry_time: [
        {
            required: true,
            message: '请选择入职时间',
            trigger: ['blur']
        }
    ],
    disable: [
        {
            required: true,
            message: '请选择在职状态',
            trigger: ['blur']
        }
    ],
    role_id: [
        {
            required: true,
            validator: roleIdValidator
        }
    ],


    dept_id: [
        {
            required: true,
            message: '请选择上级部门',
            trigger: ['blur']
        }
    ],

    jobs_id: [
        {
            required: true,
            message: '请选择岗位',
            trigger: ['blur']
        }
    ],

    password: [
        {
            required: true,
            message: '请输入密码',
            trigger: ['blur']
        }
    ]as any[],
    password_confirm: [
        {
            required: true,
            message: '请输入确认密码',
            trigger: ['blur']
        },
        {
            validator: passwordConfirmValidator,
            trigger: 'blur'
        }
    ] as any[]
})
const { optionsData } = useDictOptions<{
    role: any[]
    jobs: any[]
    dept: any[]
    adminCount: any[]
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

const handleSubmit = async () => {
    await formRef.value?.validate()
    mode.value == 'edit' ? await adminEdit(formData) : await adminAdd(formData)
    popupRef.value?.close()
    emit('success')
}

const open = async (type = 'add') => {
    mode.value = type
    const count = await adminCount();
    formData.number = count.count;
    popupRef.value?.open()
}

const setFormData = async (row: any) => {
    formRules.password = []
    formRules.password_confirm = [
        {
            validator: passwordConfirmValidator,
            trigger: 'blur'
        }
    ]
    const data = await adminDetail({
        id: row.id
    })

    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = data[key]
        }
    }

}

const handleClose = () => {
    emit('close')
}

import Upload from '@/components/upload/index.vue'


const tableData = ref([]);

const onSuccess = (file: any) => {
    console.log('上传文件成功', file.data)
    tableData.value.push(file.data);
    console.log(tableData)
}
const onChange = (file: any) => {
    tableData.value = [];
    console.log('上传文件的状态发生改变', file)
}

defineExpose({
    open,
    setFormData
})

</script>

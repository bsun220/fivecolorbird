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
<!--                <div class="flex gap-4 mb-4">
                    &lt;!&ndash; 角色选择框 &ndash;&gt;
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
                </div>-->

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
    entry_time: null,
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

defineExpose({
    open,
    setFormData
})

</script>

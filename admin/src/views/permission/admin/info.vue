<style scoped>

table {
    width: 100%;
    border-collapse: collapse; /* 合并边框 */
}

th, td {
    border: 1px solid #ddd; /* 单元格边框 */
    padding: 8px; /* 内边距 */
    text-align: left; /* 文本对齐方式 */
}

th {
    background-color: #f2f2f2; /* 表头背景色 */

}
.c1 {
    width: 20%;
    background-color: #F0F5FF;
    text-align: right;
    padding-right: 20px;
}
.c2 {
    width: 30%;
    text-align: left;
    padding-left: 20px;
}


</style>


<template>
    <div class="font-medium mb-7 title">基础信息</div>
    <table>
        <tbody>
            <tr>
                <td class="c1">名称</td>
                <td class="c2">{{formData.name}}</td>
                <td class="c1">工号</td>
                <td class="c2">{{formData.number}}</td>
            </tr>
            <tr>
                <td class="c1">任职岗位</td>
                <td class="c2">{{formData.jobs_name}}</td>
                <td class="c1">所属部门</td>
                <td class="c2">{{formData.dept_name}}</td>
            </tr>
            <tr>
                <td class="c1">性别</td>
                <td class="c2">
                    <template v-for="(item, index) in dictData.sex">
                        <span v-if="item.value == formData.sex">{{ item.name }}</span>
                    </template>
                </td>
                <td class="c1">年龄</td>
                <td class="c2">{{formData.age}}</td>
            </tr>
            <tr>
                <td class="c1">学历</td>
                <td class="c2">
                    <template v-for="(item, index) in dictData.qualification_type">
                        <span v-if="item.value == formData.qualification">{{ item.name }}</span>
                    </template>
                </td>
                <td class="c1">手机号</td>
                <td class="c2">{{formData.mobile}}</td>
            </tr>
            <tr>
                <td class="c1">邮箱地址</td>
                <td class="c2">{{formData.email}}</td>
                <td class="c1">联系地址</td>
                <td class="c2">{{formData.address}}</td>
            </tr>
            <tr>
                <td class="c1">证件类型</td>
                <td class="c2">
                    <template v-for="(item, index) in dictData.card_type">
                        <span v-if="item.value == formData.card">{{ item.name }}</span>
                    </template>
                </td>
                <td class="c1">证件号</td>
                <td class="c2">{{formData.card_val}}</td>
            </tr>
            <tr>
                <td class="c1">月薪资(元)</td>
                <td class="c2">{{formData.salary}}</td>
                <td class="c1">入职时间</td>
                <td class="c2">{{formData.entry_time}}</td>
            </tr>
            <tr >
                <td class="c1">在职状态</td>
                <td class="c2">
                    <template v-for="(item, index) in dictData.disable_type">
                        <span v-if="item.value == formData.disable">{{ item.name }}</span>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="font-medium mb-7"></div>
    <div class="font-medium mb-7 title">账号信息</div>
    <table>
        <tbody>
            <tr>
                <td class="c1">登录账号</td>
                <td class="c2">{{formData.account}}</td>
                <td class="c1">登录密码</td>
                <td class="c2">{{formData.password_mw}}</td>
            </tr>
            <tr>
                <td class="c1">角色权限</td>
                <td class="c2">{{formData.role_name}}</td>
                <td class="c1">创建时间</td>
                <td class="c2">{{formData.create_time}}</td>
            </tr>
        </tbody>
    </table>
    <div class="font-medium mb-7"></div>
    <div class="font-medium mb-7 title">附件</div>
    <div class="flex gap-4 mb-4" >
        <el-form-item label="头像">
            <material-picker v-model="formData.avatar" :disabled="true"/>
        </el-form-item>
    </div>
    <div class="flex gap-4 mb-4" >
        <el-table
            :data="tableData"
            style="width: 100%"
        >
            <el-table-column label="合同文件" >
                <template #default="{row}">
                    <a :href="row.uri" :download="row.name" target="_blank">
                        <el-button
                            type="primary"
                            link
                            :href="row.uri"
                            :download="row.name"
                            class="download-btn"
                        >
                            <el-icon><Download /></el-icon>
                            <span>{{ row.name }}</span>
                        </el-button>
                    </a>
                </template>
            </el-table-column>

        </el-table>
    </div>

</template>

<script lang="ts" setup>
import type { FormInstance } from 'element-plus'

import {deptAll} from '@/api/org/department'
import { jobsAll } from '@/api/org/post'
import { roleAll } from '@/api/perms/role'
import { useDictOptions } from '@/hooks/useDictOptions'
import type { PropType } from 'vue'
import {onMounted, ref, watch} from "vue";
import {adminDetail} from "@/api/perms/admin";

const props = defineProps({
    dictData: {
        type: Object as PropType<Record<string, any[]>>,
        default: () => ({

        })
    },
    rowId: {
        type: Number as PropType<Record<string, any>>,
        default: () => ({})
    },
})

const tableData = ref([]);
const formRef = ref<FormInstance>()
const formData = reactive({
    id: '',
    account: '',
    name: '',
    age: '',
    email: '',
    password_mw: '',

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
    contract_file:[],
    avatar: '',
    password: '',
    password_confirm: '',
    disable:'',
    multipoint_login: 1,
    root: 0,
    dept_name: '',
    jobs_name: '',
    role_name: '',
    create_time: '',

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


const loading = async () => {
    const data = await adminDetail({
        id: props.rowId
    })
    // 正确遍历对象并赋值到formData
    Object.keys(data).forEach(key => {
        formData[key] = data[key];
    });

    Object.keys(data.file_data).forEach(key => {
        tableData.value.push(data.file_data[key]);
    });

}

onMounted(() => {
    loading()
})


defineExpose({
    formRef,
    formData,
})

</script>

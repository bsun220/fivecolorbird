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
                <td class="c1">登录账号</td>
                <td class="c2">{{formData.account}}</td>
                <td class="c1">登录密码</td>
                <td class="c2">{{formData.password_mw}}</td>
            </tr>
            <tr>
                <td class="c1">角色权限</td>
                <td class="c2">{{formData.role_name}}</td>

            </tr>

        </tbody>
    </table>

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
    rowId: {
        type: Number as PropType<Record<string, any[]>>,
        default: () => ({

        })
    },

})



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

}

onMounted(() => {
    loading()
})




defineExpose({
    formRef,
    formData,
})

</script>

<template>
    <div class="edit-popup">
        <popup
            ref="popupRef"
            :title="popupTitle"
            :async="true"
            width="550px"
            @confirm="handleSubmit"
            @close="handleClose"
        >
            <el-form ref="formRef" :model="formData" label-width="90px" :rules="formRules">
                <el-form-item label="部门" prop="pid">
                    <el-select class="flex-1" v-model="formData.pid" clearable placeholder="请选择部门">
                        <el-option 
                            v-for="(item, index) in deptAllList"
                            :key="index" 
                            :label="item.name"
                            :value="item.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="公告" prop="content">
                    <el-input  type="textarea" class="w-[500px]" v-model="formData.content" clearable placeholder="请输入" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup name="noticesEdit">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { apiNoticesAdd, apiNoticesEdit, apiNoticesDetail, apiDeptAllList } from '@/api/notices'

const emit = defineEmits(['success', 'close'])
const formRef = shallowRef<FormInstance>()
const popupRef = shallowRef<InstanceType<typeof Popup>>()
const mode = ref('add')
import {onMounted} from "vue";


// 弹窗标题
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '编辑公告' : '新增公告'
})

// 表单数据
const formData = reactive({
    id: '',
    pid: '',
    content: '',
})


// 表单验证
const formRules = reactive<any>({

})

const deptAllList = ref<any[]>([]);


// 获取详情
const setFormData = async (data: Record<any, any>) => {
    for (const key in formData) {
        if (data[key] != null && data[key] != undefined) {
            //@ts-ignore
            formData[key] = data[key]
        }
    }
    
    
}

const getDetail = async (row: Record<string, any>) => {
    const data = await apiNoticesDetail({
        id: row.id
    })
    setFormData(data)
}


// 提交按钮
const handleSubmit = async () => {
    await formRef.value?.validate()
    const data = { ...formData,  }
    mode.value == 'edit' 
        ? await apiNoticesEdit(data) 
        : await apiNoticesAdd(data)
    popupRef.value?.close()
    emit('success')
}

//打开弹窗
const open = (type = 'add') => {
    mode.value = type
    popupRef.value?.open()
}

// 关闭回调
const handleClose = () => {
    emit('close')
}

defineExpose({
    open,
    setFormData,
    getDetail
})

const loading = async () => {
    const data = await apiDeptAllList();
    deptAllList.value = data;
}

onMounted(() => {
    loading()
})

</script>

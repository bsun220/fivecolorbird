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


                <el-form-item label="文件名" prop="name">
                    <el-input
                        v-model="formData.name"
                        disabled
                    />
                </el-form-item>


                <el-form-item label="文件地址" prop="url">
                    <material-picker
                        type="file"
                        v-model="formData.url"
                        @change="handleFileChange"
                        limit="1"
                    />
                </el-form-item>

            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup name="workFileEdit">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { apiWorkFileAdd, apiWorkFileEdit, apiWorkFileDetail } from '@/api/work_file'
import { timeFormat } from '@/utils/util'
import type { PropType } from 'vue'
import {fileInfo} from "@/api/file";
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


// 弹窗标题
const popupTitle = computed(() => {
    return mode.value == 'edit' ? '上传表单' : '上传表单'
})

// 表单数据
const formData = reactive({
    id: '',
    url: '',
    name: '',
})


// 表单验证
const formRules = reactive<any>({

})


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
    const data = await apiWorkFileDetail({
        id: row.id
    })
    setFormData(data)
}


// 提交按钮
const handleSubmit = async () => {
    await formRef.value?.validate()
    const data = { ...formData,  }
    mode.value == 'edit' 
        ? await apiWorkFileEdit(data) 
        : await apiWorkFileAdd(data)
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

const handleFileChange = async () => {
    if (formData.url.length > 0){
        const fileData = await fileInfo({'uri':formData.url});
        formData.file = fileData;
        formData.name = fileData[0].name;
    }
}


defineExpose({
    open,
    setFormData,
    getDetail
})
</script>

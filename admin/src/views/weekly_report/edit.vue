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
                <el-form-item label="节点" prop="node_id">
                    <el-select class="flex-1" v-model="formData.node_id" clearable placeholder="请选择节点"  @change="handleNodeChange">
                        <el-option
                            v-for="(item, index) in dictData.node_type"
                            :key="index"
                            :label="item.name"
                            :value="parseInt(item.value)"
                        />
                    </el-select>
                </el-form-item>

                <el-form-item label="周报名称" prop="file_name">
                    <el-input
                        v-model="formData.file_name"
                        disabled
                    />
                </el-form-item>


                <el-form-item label="周报" prop="file_url">
                    <material-picker
                        type="file"
                        v-model="formData.file_url"
                        @change="handleFileChange"
                        limit="1"
                    />
                </el-form-item>

            </el-form>
        </popup>
    </div>
</template>

<script lang="ts" setup name="weeklyReportEdit">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { apiWeeklyReportAdd, apiWeeklyReportEdit, apiWeeklyReportDetail } from '@/api/weekly_report'
import { timeFormat } from '@/utils/util'
import type { PropType } from 'vue'
import {fileInfo} from "@/api/file";
const props = defineProps({
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
    return mode.value == 'edit' ? '重新上传周报' : '上传周报'
})

// 表单数据
const formData = reactive({
    id: '',
    file_url: '',
    node_id: '',
    file_name: '',
    node: '',
    file:[]
})


// 表单验证
const formRules = reactive<any>({
    node_id: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],

    file_name: [{
        required: true,
        message: '请选择下方选择周报文件',
        trigger: ['blur']
    }],

    file_url: [{
        required: true,
        message: '请选择',
        trigger: ['blur']
    }],
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
    const data = await apiWeeklyReportDetail({
        id: row.id
    })
    setFormData(data)
}


// 提交按钮
const handleSubmit = async () => {
    await formRef.value?.validate()
    const data = { ...formData,  }

    mode.value == 'edit' 
        ? await apiWeeklyReportEdit(data) 
        : await apiWeeklyReportAdd(data)
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

const handleNodeChange = async (value) => {
    props.dictData.node_type.forEach(function(item, key){
        if ( value == item.value) {
            formData.node = item.name;
        }
    });
}

const handleFileChange = async () => {
    if (formData.file_url.length > 0){
        const fileData = await fileInfo({'uri':formData.file_url});
        formData.file = fileData;
        formData.file_name = fileData[0].name;
    }
}


defineExpose({
    open,
    setFormData,
    getDetail
})
</script>

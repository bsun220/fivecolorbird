<template>
    <el-form-item
        label="预约规则"
        prop="timeSlots"
        :rules="timeSlotRules"
        class="time-slot-manager"
    >
        <el-table
            :data="timeSlots"
            style="width: 100%"
            border
            empty-text="暂无时间段，请添加"
            row-key="id"
            @row-click="handleRowClick"
        >
            <el-table-column
                type="index"
                label="序号"
                width="80"
                align="center"
            >
                <template #header>
                    <el-tooltip content="添加时间段" placement="top">
                        <el-button
                            type="primary"
                            :icon="Plus"
                            circle
                            @click.stop="addTimeSlot"
                            size="small"
                            :disabled="isAddDisabled"
                        />
                    </el-tooltip>
                </template>
            </el-table-column>

            <el-table-column label="时间段" min-width="300">
                <template #default="{ row, $index }">
                    <TimeSlotItem
                        v-model:start-time="row.startTime"
                        v-model:end-time="row.endTime"
                        :min-start-time="minStartTime"
                        :max-end-time="maxEndTime"
                        @change="handleTimeChange($index)"
                    />
                    <div v-if="errors[$index]" class="error-message">
                        {{ errors[$index] }}
                    </div>
                </template>
            </el-table-column>

            <el-table-column label="操作" width="150" align="center">
                <template #default="{ $index }">
                    <el-tooltip content="删除" placement="top">
                        <el-button
                            type="danger"
                            :icon="Minus"
                            circle
                            @click.stop="removeTimeSlot($index)"
                            size="small"
                            :disabled="isRemoveDisabled"
                        />
                    </el-tooltip>

                    <el-tooltip content="上移" placement="top">
                        <el-button
                            type="info"
                            :icon="Top"
                            circle
                            @click.stop="moveUp($index)"
                            size="small"
                            :disabled="$index === 0"
                        />
                    </el-tooltip>

                    <el-tooltip content="下移" placement="top">
                        <el-button
                            type="info"
                            :icon="Bottom"
                            circle
                            @click.stop="moveDown($index)"
                            size="small"
                            :disabled="$index === timeSlots.length - 1"
                        />
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>

        <div class="time-slots-footer">
            <el-icon><InfoFilled /></el-icon>
            <span>时间段设置提示：{{ timeSlotTips }}</span>
        </div>
    </el-form-item>
</template>

<script setup>
import { Plus, Minus, Top, Bottom, InfoFilled } from '@element-plus/icons-vue'
import TimeSlotItem from './index.vue'
import { v4 as uuidv4 } from 'uuid'
import { computed, ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
        validator: (value) => {
            return value.every(item =>
                item.id &&
                typeof item.startTime === 'string' &&
                typeof item.endTime === 'string'
            )
        }
    },
    minStartTime: {
        type: String,
        default: '08:00',
        validator: (value) => /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(value)
    },
    maxEndTime: {
        type: String,
        default: '23:00',
        validator: (value) => /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/.test(value)
    },
    maxSlots: {
        type: Number,
        default: 10,
        validator: (value) => value > 0 && value <= 20
    }
})

const emit = defineEmits(['update:modelValue'])

const errors = ref([])

const timeSlots = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
})

const isAddDisabled = computed(() => timeSlots.value.length >= props.maxSlots)
const isRemoveDisabled = computed(() => timeSlots.value.length <= 1)
const timeSlotTips = computed(() => {
    if (timeSlots.value.length >= props.maxSlots) {
        return `已达到最大时间段数量限制（${props.maxSlots}个）`
    }
    return `可设置${props.maxSlots - timeSlots.value.length}个时间段，至少保留1个时间段`
})

const timeSlotRules = [
    {
        validator: validateTimeSlots,
        trigger: 'blur'
    },
    {
        validator: checkOverlap,
        trigger: 'change'
    }
]

// 方法定义
function addTimeSlot() {
    if (isAddDisabled.value) return

    const lastSlot = timeSlots.value[timeSlots.value.length - 1]
    const defaultStart = lastSlot ? addMinutes(lastSlot.endTime, 30) : props.minStartTime
    const defaultEnd = addMinutes(defaultStart, 60)

    timeSlots.value = [
        ...timeSlots.value,
        {
            id: uuidv4(),
            startTime: defaultStart,
            endTime: defaultEnd
        }
    ]
}

function removeTimeSlot(index) {
    if (isRemoveDisabled.value) return
    timeSlots.value = timeSlots.value.filter((_, i) => i !== index)
    errors.value.splice(index, 1)
}

function moveUp(index) {
    if (index <= 0) return
    swapSlots(index, index - 1)
}

function moveDown(index) {
    if (index >= timeSlots.value.length - 1) return
    swapSlots(index, index + 1)
}

function swapSlots(fromIndex, toIndex) {
    const newSlots = [...timeSlots.value]
    ;[newSlots[fromIndex], newSlots[toIndex]] = [newSlots[toIndex], newSlots[fromIndex]]
    timeSlots.value = newSlots
}

function handleTimeChange(index) {
    validateSlot(index)
    autoAdjustNextSlot(index)
}

function autoAdjustNextSlot(index) {
    if (index < timeSlots.value.length - 1) {
        const currentEnd = timeSlots.value[index].endTime
        const nextStart = timeSlots.value[index + 1].startTime

        if (nextStart < currentEnd) {
            timeSlots.value[index + 1].startTime = currentEnd
            validateSlot(index + 1)
        }
    }
}

function validateSlot(index) {
    const slot = timeSlots.value[index]
    errors.value[index] = ''

    if (!slot.startTime || !slot.endTime) {
        errors.value[index] = '请填写完整的时间段'
        return false
    }

    if (slot.startTime >= slot.endTime) {
        errors.value[index] = '开始时间必须早于结束时间'
        return false
    }

    const startMinutes = timeToMinutes(slot.startTime)
    const endMinutes = timeToMinutes(slot.endTime)

    if (startMinutes < timeToMinutes(props.minStartTime)) {
        errors.value[index] = `开始时间不能早于${props.minStartTime}`
        return false
    }

    if (endMinutes > timeToMinutes(props.maxEndTime)) {
        errors.value[index] = `结束时间不能晚于${props.maxEndTime}`
        return false
    }

    return true
}

function validateTimeSlots() {
    let isValid = true
    timeSlots.value.forEach((_, index) => {
        if (!validateSlot(index)) {
            isValid = false
        }
    })

    return isValid ? true : new Error('请检查时间段设置')
}

function checkOverlap() {
    const sortedSlots = [...timeSlots.value]
        .map(slot => ({
            start: timeToMinutes(slot.startTime),
            end: timeToMinutes(slot.endTime)
        }))
        .sort((a, b) => a.start - b.start)

    for (let i = 1; i < sortedSlots.length; i++) {
        if (sortedSlots[i].start < sortedSlots[i-1].end) {
            return new Error('时间段不能重叠')
        }
    }

    return true
}

function addMinutes(time, minutes) {
    const [hours, mins] = time.split(':').map(Number)
    const totalMinutes = hours * 60 + mins + minutes
    const newHours = Math.floor(totalMinutes / 60) % 24
    const newMinutes = totalMinutes % 60
    return `${String(newHours).padStart(2, '0')}:${String(newMinutes).padStart(2, '0')}`
}

function timeToMinutes(time) {
    const [hours, minutes] = time.split(':').map(Number)
    return hours * 60 + minutes
}

function handleRowClick(row, column) {
    if (column.property) {
        // 处理行点击事件，可根据需要扩展
    }
}

// 暴露验证方法给父组件
defineExpose({
    validateTimeSlots,
    checkOverlap
})
</script>

<style scoped>
.time-slot-manager {
:deep(.el-table__cell) {
    padding: 8px 0;
}
}

.time-slots-footer {
    margin-top: 8px;
    font-size: 12px;
    color: var(--el-text-color-secondary);
    display: flex;
    align-items: center;
    gap: 4px;
}

.error-message {
    color: var(--el-color-danger);
    font-size: 12px;
    margin-top: 4px;
}

.el-button + .el-button {
    margin-left: 4px;
}
</style>

<template>
  <div :style="styles" class="spinner spinner--gauge">
    <div :style="innerStyles" class="spinner-inner"></div>
  </div>
</template>
<script>
export default {
  props: {
    size: {
      default: '40px'
    }
  },
  computed: {
    innerStyles () {
      let size = parseInt(this.size)
      return {
        transform: 'scale(' + (size / 60) + ')'
      }
    },
    styles () {
      let size = parseInt(this.size)
      return {
        width: this.size,
        height: size / 2 + 'px'
      }
    }
  }
}
</script>
<style lang="scss" scoped>
  .spinner{
    display: flex;
    justify-content: center;
    align-items: center;
    * {
      line-height: 0;
      box-sizing: border-box;
    }
  }
  .spinner-inner {
    width: 120px;
    height: 60px;
    flex-shrink: 0;
    background: #075898;
    border-top-left-radius: 64px;
    border-top-right-radius: 64px;
    display: inline-block;
    overflow: hidden;
    position: relative;
    text-indent: -9999px;
  }
  .spinner-inner::before {
    animation: gauge-spinner 4000ms infinite ease;
    background: white;
    border-radius: 2px;
    content: '';
    position: absolute;
    left: 50%;
    bottom: 6px;
    width: 4px;
    height: 75%;
    transform-origin: 50% 100%;
  }
  .spinner-inner::after {
    content: '';
    background: white;
    border-radius: 50%;
    position: absolute;
    left: 50%;
    transform: translateX(-30%);
    bottom: 0;
    width: 12px;
    height: 12px;
  }
  @keyframes gauge-spinner {
    0% {
      transform: rotate(-50deg);
    }
    10% {
      transform: rotate(20deg);
    }
    20% {
      transform: rotate(60deg);
    }
    24% {
      transform: rotate(60deg);
    }
    40% {
      transform: rotate(-20deg);
    }
    54% {
      transform: rotate(70deg);
    }
    56% {
      transform: rotate(78deg);
    }
    58% {
      transform: rotate(73deg);
    }
    60% {
      transform: rotate(75deg);
    }
    62% {
      transform: rotate(70deg);
    }
    70% {
      transform: rotate(-20deg);
    }
    80% {
      transform: rotate(20deg);
    }
    83% {
      transform: rotate(25deg);
    }
    86% {
      transform: rotate(20deg);
    }
    89% {
      transform: rotate(25deg);
    }
    100% {
      transform: rotate(-50deg);
    }
  }
</style>

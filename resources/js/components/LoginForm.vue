<template>
  <form
    :action="loginRoute"
    method="POST"
  >
    <input
      :value="csrfToken"
      type="hidden"
      name="_token"
    >
    <div class="form-group row">
      <label
        class="col-md-4 col-form-label"
        for="email"
      >E-Mail Address</label>
      <div class="col-md-6">
        <input
          id="email"
          :value="oldInput['email']"
          :class="inputClasses('email')"
          type="email"
          name="email"
          autofocus
          required
        >
        <span
          v-if="hasError('email')"
          class="invalid-feedback"
        >
          <strong>{{ getError('email') }}</strong>
        </span>
      </div>
    </div>
    <div class="form-group row">
      <label
        class="col-md-4 col-form-label"
        for="password"
      >Password</label>
      <div class="col-md-6">
        <input
          id="password"
          :class="inputClasses('password')"
          type="password"
          name="password"
          required
        >
        <span
          v-if="hasError('password')"
          class="invalid-feedback"
        >
          <strong>{{ getError('password') }}</strong>
        </span>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-md-6 offset-md-4">
        <div class="form-check">
          <label>
            <input
              type="checkbox"
              name="remember"> Remember Me
          </label>
        </div>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-md-8 offset-md-4">
        <button
          type="submit"
          class="btn btn-primary">
          Login
        </button>

        <a
          :href="forgotPasswordRoute"
          class="btn btn-link"
        >
          Forgot Your Password?
        </a>
      </div>
    </div>
  </form>
</template>

<script>
  // Display error messages. Need truthy value and error message.
  export default {
    props: {
      oldInput: {
        type: [Array, Object],
        default: () => {return []},
      },
      validationErrors: {
        type: [Array, Object],
        default: () => {return []},
      }
    },
    data() {
      return {
        loginRoute: '/login',
        forgotPasswordRoute: '/password/reset',
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      }
    },
    methods: {
      getError(input) {
        if (this.validationErrors[input]) {
          return this.validationErrors[input][0]
        }
      },
      hasError(input) {
        return !!this.validationErrors[input]
      },
      inputClasses(input) {
        let classes = 'form-control'
        if (this.hasError(input)) {
          classes = classes + ' is-invalid'
        }
        return classes
      },
    },
  }
</script>

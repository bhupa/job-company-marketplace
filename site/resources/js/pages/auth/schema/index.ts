import { InferType, object, ref, string } from 'yup';

const email = string()
  .max(255)
  .required('emailRequired')
  .test('email', 'invalidEmail', function (value) {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    const isValidEmail = emailRegex.test(value);
    return isValidEmail;
  });

const password = string()
  .trim()
  .min(8, 'invalidPassword')
  .required('emptyPassword');

const phone = string()
  .trim()
  .required('phoneRequired')
  .test('phone', 'invalidPhone', function (value) {
    const phoneRegex = /^\+?\d{10,14}$/;
    const isValidPhone = phoneRegex.test(value);
    return isValidPhone;
  });

const emailOrPhone = string()
  .trim()
  .required('emailPhoneRequired')
  .test('email', 'invalidEmailPhone', function (value) {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    const phoneRegex = /^\d{10,14}$/;
    const isValidEmail = emailRegex.test(value);
    const isValidPhone = phoneRegex.test(value);
    return isValidEmail || isValidPhone;
  });

const loginSchema = object().shape({
  password: password,
  email: emailOrPhone,
});

export interface LoginValues extends InferType<typeof loginSchema> {}

const forgotSchema = object().shape({
  email: emailOrPhone,
});

export interface ForgotValues extends InferType<typeof forgotSchema> {}

const signupSchema = object().shape({
  phone: phone,
});

export interface SignupValues extends InferType<typeof signupSchema> {}

const enterEmailSchema = object().shape({
  email: email,
});

export interface EnterEmailValues extends InferType<typeof enterEmailSchema> {}

const enterPasswordSchema = object().shape({
  password: password,
  confirmPassword: string()
    .oneOf([ref('password')], 'passwordUnmatched')
    .required('confirmPasswordRequired'),
});

export interface EnterPasswordValues
  extends InferType<typeof enterPasswordSchema> {}

const changePasswordSchema = enterPasswordSchema.concat(
  object().shape({
    currentPassword: password,
  }),
);

export interface ChangePasswordValues
  extends InferType<typeof changePasswordSchema> {}

export {
  loginSchema,
  forgotSchema,
  signupSchema,
  enterEmailSchema,
  enterPasswordSchema,
  changePasswordSchema,
};

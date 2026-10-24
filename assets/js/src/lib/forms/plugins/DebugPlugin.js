export function DebugPlugin() {
  return {
    init(i) {
      console.log('[debug] init', i.form);
    },
    onValidateStart(i) {
      console.log('[debug] validate:start');
    },
    validateField(i, field) {
      // ВАЖНО: возвращаем undefined, чтобы не перехватывать логику!
      console.log('[debug] field', field.name, 'value=', i.getValue(field));
      return undefined;
    },
    onFieldInvalid(i, field, msg) {
      console.log('[debug] INVALID', field.name, msg);
    },
    onFieldValid(i, field) {
      console.log('[debug] VALID', field.name);
    },
    onValidateEnd(i, ok) {
      console.log('[debug] validate:end ok=', ok);
    },
    onSubmitFail() {
      console.log('[debug] submit:fail');
    },
    onSubmitSuccess() {
      console.log('[debug] submit:success');
    },
  };
}

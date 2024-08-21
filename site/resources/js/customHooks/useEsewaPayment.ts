import CryptoJS from 'crypto-js';

const {
  VITE_ESEWA_BASE_URL,
  VITE_ESEWA_PREFIX_URL,
  VITE_ESEWA_PRODUCT_CODE,
  VITE_ESEWA_SECRET_KEY,
  VITE_BASE_URL,
} = import.meta.env;

const useEsewaPayment = () => {
  const makePayment = (params: IEsewaFormData) => {
    const { total_amount, transaction_uuid } = params;

    const currentTime = new Date();
    // YYMMDD-HHMMSS
    const transactionUuid =
      transaction_uuid +
      '-' +
      currentTime.toISOString().slice(2, 10).replace(/-/g, '') +
      '-' +
      currentTime.getHours() +
      currentTime.getMinutes() +
      currentTime.getSeconds();

    const message = `total_amount=${total_amount},transaction_uuid=${transactionUuid},product_code=${VITE_ESEWA_PRODUCT_CODE}`;
    const secretKey = VITE_ESEWA_SECRET_KEY;

    const signatureHash = CryptoJS.HmacSHA256(message, secretKey).toString(
      CryptoJS.enc.Base64,
    );

    const form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', VITE_ESEWA_BASE_URL + VITE_ESEWA_PREFIX_URL);

    const extraParams = {
      transaction_uuid: transactionUuid,
      product_code: VITE_ESEWA_PRODUCT_CODE,
      signature: signatureHash,
      failure_url: VITE_BASE_URL,
      signed_field_names: 'total_amount,transaction_uuid,product_code',
    };

    const allParams = {
      ...params,
      ...extraParams,
    };

    // Create input fields
    for (const key in allParams) {
      const hiddenField = document.createElement('input');
      hiddenField.setAttribute('type', 'hidden');
      hiddenField.setAttribute('name', key);
      hiddenField.setAttribute('value', allParams[key as keyof IEsewaFormData]);
      form.appendChild(hiddenField);
    }

    document.body.appendChild(form);
    form.submit();
  };

  return {
    makePayment,
  };
};

export default useEsewaPayment;

export interface IEsewaFormData {
  amount: string;
  product_delivery_charge: string;
  product_service_charge: string;
  success_url: string;
  tax_amount: string;
  total_amount: string;
  transaction_uuid: string;
}

/**
 * success_url: `${VITE_BASE_URL}esewa/epay?url=chat&platform=web&type=unrestricted-chat&room_id=${roomId}&user_id=${user.id}&price_id=${priceId}&`,
 * PARAMS:
 * url => return url from our server after saving esewa response
 * url_value => Optional, if provided, return url will be url/url_value
 * platform => web | mobile => paid from mobile app or website
 * type => unrestricted-chat | subscription => unrestricted-chat for superchat, subscription for subscription plans
 * room_id => required for type = unrestricted-chat only to determine chatroom id for which payment is done
 * user_id => required for all cases to determine which user
 * price_id => required to determine plan for which payment is done
 */

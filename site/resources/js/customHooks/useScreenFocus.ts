import { useState, useEffect } from 'react';

const useScreenFocus = () => {
  const [focused, setFocused] = useState(true);

  useEffect(() => {
    const handleFocus = () => {
      setFocused(true);
    };

    const handleBlur = () => {
      setFocused(false);
    };

    window.addEventListener('focus', handleFocus);
    window.addEventListener('blur', handleBlur);

    return () => {
      window.removeEventListener('focus', handleFocus);
      window.removeEventListener('blur', handleBlur);
    };
  }, []);

  return focused;
};

export default useScreenFocus;

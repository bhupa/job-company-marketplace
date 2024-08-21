import { useEffect } from 'react';

import TagManager from 'react-gtm-module';
import { useLocation } from 'react-router-dom';

const sendPageView = (url: string) => {
  TagManager.dataLayer({
    dataLayer: {
      event: 'pageview',
      page: url,
    },
  });
};

const usePageView = () => {
  const location = useLocation();

  useEffect(() => {
    sendPageView(location.pathname + location.search);
  }, [location]);
};

export default usePageView;

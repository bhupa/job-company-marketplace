import React from 'react';

// Components
import Header from './Header';
import Footer from './Footer';

// Hooks
import usePageView from '@customHooks/usePageView';

interface Props {
  children: React.ReactNode;
}

const AuthWrapper = (props: Props) => {
  usePageView();
  return (
    <div className="overflow-scroll flex flex-col h-screen">
      <Header />
      <div className="h-[calc(100vh-64px)] flex flex-col">
        <div className="max-w-5xl w-full py-4 px-3 xs:px-12 flex flex-1 items-center justify-center mx-auto mb-4">
          {props.children}
        </div>
        <Footer />
      </div>
    </div>
  );
};

export default AuthWrapper;

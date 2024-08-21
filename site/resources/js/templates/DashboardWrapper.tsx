import React from 'react';

// Components
import DashboardHeader from './DashboardHeader';
import DashboardFooter from './DashboardFooter';

// Hooks
import usePageView from '@customHooks/usePageView';

interface Props {
  children: React.ReactNode;
  className?: string;
}

const DashboardWrapper = (props: Props) => {
  usePageView();
  return (
    <div className="overflow-scroll flex flex-col h-screen">
      <DashboardHeader />
      <div
        className={`h-[calc(100vh-64px)] flex flex-col overflow-scroll ${props.className}`}>
        <div
          className={
            'max-w-5xl w-full py-4 px-4 sm:px-12 flex flex-1 mx-auto mb-4'
          }>
          {props.children}
        </div>
        <DashboardFooter />
      </div>
    </div>
  );
};

export default DashboardWrapper;

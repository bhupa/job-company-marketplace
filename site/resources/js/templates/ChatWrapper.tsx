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
        className={`flex flex-1 flex-col overflow-scroll ${props.className || ''}`}>
        <div
          className={
            'max-w-5xl w-full flex flex-1 mx-auto h-[calc(100%)] py-4 px-6 sm:px-12 overflow-scroll'
          }>
          {props.children}
        </div>
      </div>
      <DashboardFooter />
    </div>
  );
};

export default DashboardWrapper;

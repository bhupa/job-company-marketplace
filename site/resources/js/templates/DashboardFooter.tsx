import React from 'react';
import { useLocation, useNavigate } from 'react-router-dom';

// Hooks
import useUserProfile from '@customHooks/useUserProfile';

// Redux
import { useAppSelector } from '@redux/hook';
import { UserType } from '@redux/reducers/auth';

//others
import { Bookmark, Chat, Home, User, Work } from '@assets/icons';

const iconProps = {
  width: 28,
  height: 28,
};

const DashboardFooter = () => {
  const { userType } = useUserProfile();
  const navigate = useNavigate();
  const location = useLocation();

  const { isChatPolicyAccepted, tabUnseenCount } = useAppSelector(
    state => state.chat,
  );

  const path = location?.pathname ?? '';
  const focusedColor = 'text-BLUE_004D80';
  const unfocusedColor = 'text-GRAY_77838F';

  return (
    <footer className="sticky bottom-0 flex flex-row justify-around gap-2 py-2 bg-white border-t-[2px] border-t-WHITE_F6F6F6 z-[1000]">
      {TAB_MENUS.map(item => {
        if (item?.hidden === userType) {
          return null;
        }
        const Icon = item.icon;
        let route = item.route;
        const isChatTab = item.name === 'chat';
        if (isChatTab && !isChatPolicyAccepted) {
          route = '/chat-policy';
        }
        const isFocused = route === path;
        return (
          <div key={item.name} className="relative">
            {isChatTab && tabUnseenCount > 0 && (
              <div className="h-2 w-2 rounded-full flex absolute -top-1 -right-2 bg-RED_FF4D4D" />
            )}
            <Icon
              {...iconProps}
              className={`cursor-pointer ${isFocused ? focusedColor : unfocusedColor}`}
              onClick={() => navigate(route)}
            />
          </div>
        );
      })}
    </footer>
  );
};

export default DashboardFooter;

const TAB_MENUS = [
  {
    name: 'home',
    route: '/home',
    icon: Home,
  },
  {
    name: 'bookmark',
    route: '/bookmark',
    icon: Bookmark,
  },
  {
    name: 'chat',
    route: '/chat',
    icon: Chat,
  },
  {
    name: 'jobs',
    route: '/jobs',
    icon: Work,
    hidden: UserType.JobSeeker,
  },
  {
    name: 'profile',
    route: '/profile',
    icon: User,
  },
];

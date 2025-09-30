// icons
import {
  QuestionOutlined,
  DashboardOutlined,
  ChromeOutlined,
  LoginOutlined,
  ProfileOutlined,
  FontSizeOutlined,
  BgColorsOutlined,
  BarcodeOutlined,
  CrownOutlined,
  PlusOutlined,
  FileOutlined,
  BarChartOutlined
} from '@ant-design/icons-vue';

export interface menu {
  header?: string;
  title?: string;
  icon?: object;
  to?: string;
  divider?: boolean;
  chip?: string;
  chipColor?: string;
  chipVariant?: string;
  chipIcon?: string;
  children?: menu[];
  disabled?: boolean;
  type?: string;
  subCaption?: string;
}

const sidebarItem: menu[] = [
  { header: 'Navigation' },
  {
    title: 'Dashboard',
    icon: DashboardOutlined,
    to: '/dashboard'
  },
  // { header: 'Authentication' },
  // {
  //   title: 'Login',
  //   icon: LoginOutlined,
  //   to: '/login1'
  // },
  // {
  //   title: 'Register',
  //   icon: ProfileOutlined,
  //   to: '/register'
  // },
  { header: 'Utilities' },
  {
    title: 'Forms',
    icon: FileOutlined,
    to: '/typography'
  },
  {
    title: 'Form Builder',
    icon: PlusOutlined,
    to: '/colors'
  },
  {
    title: 'Submissions',
    icon: BarChartOutlined,
    to: '/shadow'
  },
  {
    title: 'Preview',
    icon: CrownOutlined,
    to: '/icon/ant'
  },
  { header: 'Support' },
  {
    title: 'Settings',
    icon: ChromeOutlined,
    to: '/sample-page'
  },
  // {
  //   title: 'Documentation',
  //   icon: QuestionOutlined,
  //   to: 'https://codedthemes.gitbook.io/mantis-vuetify/',
  //   type: 'external',
  //   chip: 'gitbook',
  //   chipColor: 'secondary',
  //   chipVariant: 'flat'
  // }
];

export default sidebarItem;
